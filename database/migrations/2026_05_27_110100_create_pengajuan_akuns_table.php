<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_akuns', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->index();
            $table->string('no_hp', 20);
            $table->string('password_hash');
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending')->index();
            $table->string('alasan_tolak', 500)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_akuns');
    }
};
