<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom `kategori` di tabel `jadwals` tidak pernah dibuat karena migrasi
     * 2026_05_23_add_kategori_to_jadwals_table salah menargetkan tabel `laporans`.
     * Migrasi ini memperbaiki database yang sudah pernah menjalankan migrasi tersebut.
     */
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwals', 'kategori')) {
                $table->string('kategori')->nullable()->after('lokasi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
