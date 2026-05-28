<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->unique();
            }
            if (!Schema::hasColumn('users', 'no_kk')) {
                $table->string('no_kk', 16)->nullable()->index();
            }
            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'status_keluarga')) {
                // kepala_keluarga, istri, anak, lainnya
                $table->string('status_keluarga', 30)->nullable();
            }
            if (!Schema::hasColumn('users', 'is_kepala_keluarga')) {
                $table->boolean('is_kepala_keluarga')->default(false);
            }
            if (!Schema::hasColumn('users', 'pekerjaan')) {
                $table->string('pekerjaan', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'akun_aktif')) {
                $table->boolean('akun_aktif')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'no_kk', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
                'status_keluarga', 'is_kepala_keluarga', 'pekerjaan', 'akun_aktif',
            ]);
        });
    }
};
