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

            // Gagal di level HTTP (4xx/5xx/timeout)
            if (!$response->successful()) {
                $err = 'HTTP ' . $response->status() . ': ' . $response->body();
                Log::error('[Fonnte] Gagal mengirim pesan', ['target' => $normalized, 'response' => $err]);
                $this->log($user, $normalized, $jenis, $pesan, 'gagal', $err);
                return false;
            }

            // Fonnte selalu membalas HTTP 200 walau pesan gagal terkirim
            // (mis. nomor tidak terdaftar WA, token invalid, rate limit),
            // lewat field "status" di body JSON. Jangan hanya andalkan HTTP 2xx.
            $body   = $response->json();
            $raw    = $response->body();
            $status = is_array($body) && array_key_exists('status', $body)
                ? filter_var($body['status'], FILTER_VALIDATE_BOOL)
                : false; // Status tidak diketahui ≠ sukses (hindari false positive)

            // Log mentah selalu dicatat agar mudah menelusuri di tahap mana gagal.
            Log::debug('[Fonnte] Respon mentah', [
                'target' => $normalized,
                'jenis'  => $jenis,
                'status' => $status,
                'body'   => mb_substr($raw, 0, 500),
            ]);

            if ($status) {
                Log::info('[Fonnte] Pesan terkirim', ['target' => $normalized, 'jenis' => $jenis]);
                $this->log($user, $normalized, $jenis, $pesan, 'terkirim');
                return true;
            }

            $reason = is_array($body) && !empty($body['reason'])
                ? $body['reason']
                : (mb_substr($raw, 0, 300) ?: 'Response tanpa field status');
            $err = 'Fonnte menolak: ' . $reason;
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
     * Bila $useQueue null → mengikuti config 'fonnte.queue' (default sync),
     * karena broadcast lewat queue baru benar-benar terkirim bila worker
     * 'php artisan queue:work' berjalan di server.
     */
    public function kirimKeUsers(iterable $users, string $pesan, string $jenis = 'manual', ?bool $useQueue = null): int
    {
        $useQueue ??= (bool) config('fonnte.queue', false);

        $targetUsers = collect($users)
            ->filter(fn ($u) => $u instanceof User && $u->bisaTerimaWa())
            ->values();

        if ($targetUsers->isEmpty()) {
            Log::warning('[Fonnte] kirimKeUsers tanpa target', ['jenis' => $jenis]);
            return 0;
        }

        $mode = ($useQueue && config('queue.default') !== 'sync') ? 'queue' : 'sync';
        Log::info('[Fonnte] kirimKeUsers mulai', [
            'jenis'  => $jenis,
            'target' => $targetUsers->count(),
            'mode'   => $mode,
        ]);

        // Jika menggunakan queue, dispatch job per user dengan delay batching.
        // Catatan: jangan pakai app()->bound('queue') — QueueManager selalu
        // ter-register sehingga cek tsb tidak pernah berguna. Guard yang benar
        // adalah koneksi queue aktif; bila 'sync' fallback ke eksekusi langsung.
        if ($mode === 'queue') {
            $targetUsers->each(function (User $user, int $index) use ($pesan, $jenis) {
                dispatch(
                    new \App\Jobs\KirimWaJob($user->no_hp, $pesan, $jenis, $user)
                )->delay(now()->addSeconds($index * 2)); // Delay 2 detik antar pesan
            });
            Log::info('[Fonnte] Broadcast masuk antrean queue', [
                'jenis'  => $jenis,
                'jumlah' => $targetUsers->count(),
            ]);
            return $targetUsers->count();
        }

        // Eksekusi langsung (mode sync)
        $sukses   = 0;
        $attempts = 0;
        foreach ($targetUsers as $u) {
            $attempts++;
            if ($this->kirim($u->no_hp, $pesan, $jenis, $u)) {
                $sukses++;
            }
            // Delay kecil per 10 percobaan (sukses maupun gagal) untuk
            // menghindari rate limit Fonnte.
            if ($attempts % 10 === 0) {
                usleep(500000); // 0.5 detik
            }
        }
        Log::info('[Fonnte] Broadcast selesai', ['jenis' => $jenis, 'sukses' => $sukses, 'attempts' => $attempts]);
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
        if ($nomor === null || trim($nomor) === '') {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $nomor);
        if ($clean === null || $clean === '') {
            return null;
        }

        // "6208123456789" (kode negara + 0 rangkap) -> "628123456789"
        if (str_starts_with($clean, $this->countryCode . '0')) {
            $clean = $this->countryCode . substr($clean, strlen($this->countryCode) + 1);
        }

        if (str_starts_with($clean, '0')) {
            // Format lokal "0xxx" / "00xxx": buang SEMUA 0 di awal.
            $clean = ltrim($clean, '0');
            // Bila sisanya sudah memuat kode negara ("00628..." -> "628..."),
            // biarkan; selain itu tambahkan kode negara ("0812..." -> "62812...").
            if (!str_starts_with($clean, $this->countryCode)) {
                $clean = $this->countryCode . $clean;
            }
        } elseif (!str_starts_with($clean, $this->countryCode) && str_starts_with($clean, '8')) {
            // Format lokal tanpa awalan 0/62: "812xxx..." -> "62812xxx..."
            $clean = $this->countryCode . $clean;
        }
        // Selain itu dianggap sudah format internasional (mis. "6012..." nomor
        // luar negeri) — dibiarkan apa adanya, tidak dipaksa ke kode negara lokal.

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
