<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kontak
            if (!Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 20)->nullable();
            }
            if (!Schema::hasColumn('users', 'status_warga')) {
                $table->enum('status_warga', ['tetap', 'kontrak', 'kos', 'lainnya'])
                    ->default('tetap');
            }

            // Alamat detail (manual)
            if (!Schema::hasColumn('users', 'alamat_detail')) {
                $table->string('alamat_detail', 500)->nullable();
            }
            if (!Schema::hasColumn('users', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable();
            }

            // Wilayah Indonesia (ID + nama disimpan agar tetap konsisten saat API berubah)
            if (!Schema::hasColumn('users', 'provinsi_id')) {
                $table->string('provinsi_id', 10)->nullable();
                $table->string('provinsi_nama', 100)->nullable();
                $table->string('kota_id', 10)->nullable();
                $table->string('kota_nama', 100)->nullable();
                $table->string('kecamatan_id', 15)->nullable();
                $table->string('kecamatan_nama', 100)->nullable();
                $table->string('kelurahan_id', 20)->nullable();
                $table->string('kelurahan_nama', 100)->nullable();
            }

            // Notifikasi
            if (!Schema::hasColumn('users', 'notif_wa_aktif')) {
                $table->boolean('notif_wa_aktif')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'no_hp', 'status_warga',
                'alamat_detail', 'kode_pos',
                'provinsi_id', 'provinsi_nama',
                'kota_id', 'kota_nama',
                'kecamatan_id', 'kecamatan_nama',
                'kelurahan_id', 'kelurahan_nama',
                'notif_wa_aktif',
            ]);
        });
    }
};
