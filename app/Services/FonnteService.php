<?php

namespace App\Services;

use App\Models\NotifikasiLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk mengirim notifikasi WhatsApp via Fonnte.
 *
 * Pemakaian:
 *   app(FonnteService::class)->kirim('081234567890', 'Halo', 'manual');
 *   app(FonnteService::class)->kirimKeUsers($users, 'Pesan...', 'jadwal_baru');
 *
 * Setiap pengiriman dicatat ke tabel notifikasi_logs.
 */
class FonnteService
{
    protected string $token;
    protected string $endpoint;
    protected bool $enabled;
    protected string $countryCode;
    protected int $timeout;

    public function __construct()
    {
        $this->token       = (string) config('fonnte.token');
        $this->endpoint    = (string) config('fonnte.endpoint');
        $this->enabled     = (bool)   config('fonnte.enabled', true);
        $this->countryCode = (string) config('fonnte.country_code', '62');
        $this->timeout     = (int)    config('fonnte.timeout', 15);
    }

    /**
     * Kirim WA ke satu nomor.
     *
     * @param  string       $nomor
     * @param  string       $pesan
     * @param  string       $jenis  Untuk audit (lihat NotifikasiLog::jenisLabel)
     * @param  User|null    $user   Optional, jika dikenal untuk relasi
     * @return bool
     */
    public function kirim(string $nomor, string $pesan, string $jenis = 'manual', ?User $user = null): bool
    {
        $normalized = $this->normalisasiNomor($nomor);

        if (!$this->enabled) {
            Log::info('[Fonnte] Disabled, pesan tidak dikirim', ['nomor' => $nomor]);
            $this->log($user, $normalized ?? $nomor, $jenis, $pesan, 'gagal', 'Fonnte disabled');
            return false;
        }

        if (empty($this->token)) {
            Log::warning('[Fonnte] FONNTE_TOKEN belum dikonfigurasi');
            $this->log($user, $normalized ?? $nomor, $jenis, $pesan, 'gagal', 'Token Fonnte belum dikonfigurasi');
            return false;
        }

        if (!$normalized) {
            Log::warning('[Fonnte] Nomor tidak valid', ['nomor' => $nomor]);
            $this->log($user, $nomor, $jenis, $pesan, 'gagal', 'Nomor tidak valid');
            return false;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $this->token])
                ->timeout($this->timeout)
                ->asForm()
                ->post($this->endpoint, [
                    'target'      => $normalized,
                    'message'     => $pesan,
                    'countryCode' => $this->countryCode,
                ]);

            if ($response->successful()) {
                Log::info('[Fonnte] Pesan terkirim', ['target' => $normalized]);
                $this->log($user, $normalized, $jenis, $pesan, 'terkirim');
                return true;
            }

            $err = 'HTTP ' . $response->status() . ': ' . $response->body();
            Log::error('[Fonnte] Gagal mengirim pesan', ['target' => $normalized, 'response' => $err]);
            $this->log($user, $normalized, $jenis, $pesan, 'gagal', $err);
            return false;
        } catch (\Throwable $e) {
            Log::error('[Fonnte] Exception saat kirim WA', ['target' => $normalized, 'error' => $e->getMessage()]);
            $this->log($user, $normalized, $jenis, $pesan, 'gagal', $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim ke banyak user. Hanya user notif_wa_aktif=true & punya no_hp.
     * Gunakan queue untuk menghindari rate limit jika banyak user.
     */
    public function kirimKeUsers(iterable $users, string $pesan, string $jenis = 'manual', bool $useQueue = false): int
    {
        $targetUsers = collect($users)
            ->filter(fn ($u) => $u instanceof User && $u->bisaTerimaWa())
            ->values();

        if ($targetUsers->isEmpty()) {
            return 0;
        }

        // Jika menggunakan queue, dispatch job per user dengan delay batching
        if ($useQueue && app()->bound('queue')) {
            $targetUsers->each(function (User $user, int $index) use ($pesan, $jenis) {
                dispatch(
                    new \App\Jobs\KirimWaJob($user->no_hp, $pesan, $jenis, $user)
                )->delay(now()->addSeconds($index * 2)); // Delay 2 detik antar pesan
            });
            return $targetUsers->count();
        }

        // Eksekusi langsung (mode sync)
        $sukses = 0;
        foreach ($targetUsers as $u) {
            if ($this->kirim($u->no_hp, $pesan, $jenis, $u)) {
                $sukses++;
            }
            // Delay kecil antar pesan untuk menghindari rate limit
            if ($sukses > 0 && $sukses % 10 === 0) {
                usleep(500000); // 0.5 detik setiap 10 pesan
            }
        }
        return $sukses;
    }

    /**
     * Kirim ulang pesan dari log yang gagal.
     */
    public function retry(NotifikasiLog $log): bool
    {
        $ok = $this->kirim($log->nomor_tujuan, $log->pesan, $log->jenis, $log->user);
        $log->increment('retry_count');
        return $ok;
    }

    public function normalisasiNomor(?string $nomor): ?string
    {
        if (!$nomor) return null;

        $clean = preg_replace('/[^0-9]/', '', $nomor);
        if (!$clean) return null;

        if (str_starts_with($clean, '0')) {
            $clean = $this->countryCode . substr($clean, 1);
        }

        if (!str_starts_with($clean, $this->countryCode)) {
            $clean = $this->countryCode . ltrim($clean, '0');
        }

        if (strlen($clean) < strlen($this->countryCode) + 8) {
            return null;
        }

        return $clean;
    }

    protected function log(?User $user, string $nomor, string $jenis, string $pesan, string $status, ?string $error = null): void
    {
        try {
            NotifikasiLog::create([
                'user_id'      => $user?->id,
                'nomor_tujuan' => $nomor,
                'jenis'        => $jenis,
                'pesan'        => $pesan,
                'status'       => $status,
                'error'        => $error,
            ]);
        } catch (\Throwable $e) {
            // Jangan biarkan log issue menggagalkan pengiriman
            Log::error('[Fonnte] Gagal menyimpan NotifikasiLog', ['error' => $e->getMessage()]);
        }
    }
}
