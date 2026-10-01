<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job untuk mengirim notifikasi WhatsApp via queue.
 * Membantu menghindari rate limit Fonnte saat mengirim ke banyak user.
 */
class KirimWaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;
    public int $timeout = 30;

    public function __construct(
        public string $nomor,
        public string $pesan,
        public string $jenis = 'manual',
        public ?User $user = null
    ) {}

    public function handle(FonnteService $fonnte): void
    {
        $sukses = $fonnte->kirim($this->nomor, $this->pesan, $this->jenis, $this->user);

        if (!$sukses) {
            // THROW wajib: tanpa ini Laravel menganggap job berhasil walau pesan
            // gagal terkirim, sehingga tries/backoff/failed() tidak pernah jalan.
            Log::warning('[KirimWaJob] Gagal mengirim WA, job akan di-retry', [
                'nomor' => $this->nomor,
                'jenis' => $this->jenis,
                'attempt' => $this->attempts(),
            ]);

            throw new \RuntimeException(
                'Gagal mengirim WhatsApp ke ' . $this->nomor . ' (jenis: ' . $this->jenis . ')'
            );
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[KirimWaJob] Job gagal setelah max tries', [
            'nomor'  => $this->nomor,
            'jenis'  => $this->jenis,
            'error'  => $exception->getMessage(),
        ]);
    }
}
