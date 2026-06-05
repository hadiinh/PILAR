<?php

namespace App\Services;

use App\Mail\OtpResetPasswordMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Mengelola OTP untuk fitur Lupa Kata Sandi.
 *  - OTP 6 digit numerik
 *  - Disimpan dalam bentuk hash (Hash::make)
 *  - Berlaku 10 menit (config: auth.otp_ttl_minutes)
 *  - Single-use (used_at distempel saat verifikasi sukses)
 *  - Rate limit request & percobaan verifikasi
 *  - Selalu return pesan generic agar tidak bocorkan keberadaan akun.
 */
class OtpService
{
    public const TTL_MINUTES         = 10;
    public const MAX_VERIFY_ATTEMPTS = 5;

    /**
     * Request OTP. $identifier bisa NIK atau email.
     *
     * @return array{ok: bool, message: string, throttled: bool}
     */
    public function request(string $identifier, ?string $ip = null): array
    {
        $identifier = trim($identifier);
        $genericMsg = 'Jika data cocok dan email terdaftar, kode OTP akan dikirim ke email Anda.';

        if ($identifier === '') {
            return ['ok' => true, 'message' => $genericMsg, 'throttled' => false];
        }

        $user = $this->findUser($identifier);

        // Rate limit per IP (selalu di-hit, baik user ada atau tidak).
        $ipKey = 'otp:request:ip:' . ($ip ?: 'unknown');
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            return [
                'ok'        => false,
                'message'   => 'Terlalu banyak permintaan dari perangkat ini. Coba lagi nanti.',
                'throttled' => true,
            ];
        }
        RateLimiter::hit($ipKey, 3600);

        // Jika user tidak ditemukan atau tidak punya email, return generic tanpa mengirim apa pun.
        if (!$user || !$user->email) {
            return ['ok' => true, 'message' => $genericMsg, 'throttled' => false];
        }

        $emailLower = strtolower($user->email);

        // Rate limit per email
        $emailKey = 'otp:request:email:' . $emailLower;
        if (RateLimiter::tooManyAttempts($emailKey, 3)) {
            return [
                'ok'        => false,
                'message'   => 'Permintaan OTP terlalu sering untuk email ini. Coba lagi dalam 1 jam.',
                'throttled' => true,
            ];
        }
        RateLimiter::hit($emailKey, 3600);

        // Generate OTP 6 digit
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(config('auth.otp_ttl_minutes', self::TTL_MINUTES));

        // Invalidate OTP aktif yang lama untuk email ini
        PasswordResetOtp::where('email', $emailLower)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        PasswordResetOtp::create([
            'user_id'    => $user->id,
            'email'      => $emailLower,
            'otp_hash'   => Hash::make($otp),
            'attempts'   => 0,
            'expires_at' => $expiresAt,
            'used_at'    => null,
            'request_ip' => $ip,
        ]);

        try {
            Mail::to($user->email)->send(new OtpResetPasswordMail($user, $otp, $expiresAt));
        } catch (\Throwable $e) {
            Log::error('[OtpService] Gagal mengirim OTP email', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            // Tetap return generic message agar tidak bocorkan info.
        }

        return ['ok' => true, 'message' => $genericMsg, 'throttled' => false];
    }

    /**
     * Verifikasi OTP. Return User jika valid, null jika gagal.
     */
    public function verify(string $email, string $otp): ?User
    {
        $emailLower = strtolower(trim($email));

        $verifyKey = 'otp:verify:' . $emailLower;
        if (RateLimiter::tooManyAttempts($verifyKey, 10)) {
            return null;
        }
        RateLimiter::hit($verifyKey, 900); // 15 menit

        $record = PasswordResetOtp::aktif($emailLower)->first();
        if (!$record) {
            return null;
        }

        $record->increment('attempts');

        if ($record->attempts > self::MAX_VERIFY_ATTEMPTS) {
            $record->update(['used_at' => now()]);
            return null;
        }

        if (!Hash::check($otp, $record->otp_hash)) {
            return null;
        }

        $record->update(['used_at' => now()]);

        return $record->user;
    }

    public function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) return $email;
        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, 1);
        $masked  = $visible . str_repeat('*', max(1, mb_strlen($local) - 1));
        return $masked . '@' . $domain;
    }

    /**
     * Cari user berdasar NIK atau email (case-insensitive).
     */
    protected function findUser(string $identifier): ?User
    {
        // NIK = 16 digit angka
        if (preg_match('/^\d{16}$/', $identifier)) {
            return User::query()
                ->where('nik', $identifier)
                ->where('akun_aktif', true)
                ->first();
        }

        return User::query()
            ->whereRaw('LOWER(email) = ?', [strtolower($identifier)])
            ->where('akun_aktif', true)
            ->first();
    }
}
