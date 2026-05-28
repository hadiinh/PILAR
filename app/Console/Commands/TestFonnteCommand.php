<?php

namespace App\Console\Commands;

use App\Services\FonnteService;
use Illuminate\Console\Command;

class TestFonnteCommand extends Command
{
    protected $signature = 'fonnte:test {nomor : Nomor HP tujuan (mis. 08123456789)} {--pesan=Tes notifikasi PILAR RW 016}';

    protected $description = 'Kirim pesan WhatsApp uji coba ke nomor tertentu menggunakan FonnteService';

    public function handle(FonnteService $fonnte): int
    {
        $nomor = (string) $this->argument('nomor');
        $pesan = (string) $this->option('pesan');

        $this->info('Mengirim pesan ke: ' . $nomor);
        $ok = $fonnte->kirim($nomor, $pesan);

        if ($ok) {
            $this->info('Berhasil. Periksa WhatsApp tujuan.');
            return self::SUCCESS;
        }

        $this->error('Gagal mengirim. Cek log Laravel untuk detail.');
        return self::FAILURE;
    }
}
