<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_tujuan', 30)->index();
            $table->string('jenis', 60)->index();   // jadwal_baru, kegiatan_baru, laporan_baru, laporan_status, pengajuan_disetujui, pengajuan_ditolak, manual
            $table->text('pesan');
            $table->enum('status', ['terkirim', 'gagal'])->index();
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_logs');
    }
};
