<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('akun_aktif');
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            }
            if (!Schema::hasColumn('users', 'session_version')) {
                $table->unsignedInteger('session_version')->default(0)->after('password_changed_at');
            }
        });

        // Dedupe email sebelum apply unique constraint: pertahankan id terkecil per email.
        $duplicates = DB::table('users')
            ->select('email')
            ->whereNotNull('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        foreach ($duplicates as $email) {
            $keepId = DB::table('users')->where('email', $email)->min('id');
            DB::table('users')
                ->where('email', $email)
                ->where('id', '!=', $keepId)
                ->update(['email' => null]);
        }

        // Tambah unique constraint pada email; biarkan exception jika index sudah ada.
        try {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('email');
            });
        } catch (\Throwable $e) {
            // Index sudah ada atau tidak didukung driver - cukup di-log.
            report($e);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique(['email']);
            } catch (\Throwable $e) {
                // ignore - index mungkin tidak ada
            }

            $table->dropColumn(['must_change_password', 'password_changed_at', 'session_version']);
        });
    }
};
