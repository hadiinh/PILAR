<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status kegiatan dihitung otomatis dari tanggal (tidak disimpan di database),
     * diganti dengan kolom `kategori` untuk keperluan filter.
     */
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatans', 'kategori')) {
                $table->string('kategori')->nullable()->after('judul');
            }
            if (Schema::hasColumn('kegiatans', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatans', 'status')) {
                $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru')->after('tanggal');
            }
            if (Schema::hasColumn('kegiatans', 'kategori')) {
                $table->dropColumn('kategori');
            }
        });
    }
};
