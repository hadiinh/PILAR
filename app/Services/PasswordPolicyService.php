<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Logic terpusat untuk validasi & generate password.
 * Aturan:
 *  - Minimal 8 karakter
 *  - Ada huruf besar, huruf kecil, angka, simbol
 *  - Tidak boleh sama dengan NIK user (jika user diberikan)
 *  - Tidak boleh sama dengan no_hp (digit-only) user (jika user diberikan)
 *  - Tidak boleh sama dengan password lama (jika currentHash diberikan)
 */
class PasswordPolicyService
{
    public const MIN_LENGTH = 8;
    public const SYMBOLS    = '!@#$%^&*()-_=+[]{};:,.?/';

    /**
     * @return array<string>  daftar pesan error (kosong = valid)
     */
    public function validate(string $password, ?User $user = null, ?string $currentHash = null): array
    {
        $errors = [];

        if (mb_strlen($password) < self::MIN_LENGTH) {
            $errors[] = 'Password minimal ' . self::MIN_LENGTH . ' karakter.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf besar.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password harus mengandung huruf kecil.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password harus mengandung angka.';
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'\"<>,.?\/]/', $password)) {
            $errors[] = 'Password harus mengandung simbol (!@#$%^&* dll).';
        }

        if ($user) {
            if (!empty($user->nik) && $password === $user->nik) {
                $errors[] = 'Password tidak boleh sama dengan NIK.';
            }
            $noHpDigit = $user->no_hp ? preg_replace('/[^0-9]/', '', $user->no_hp) : null;
            $passwordDigit = preg_replace('/[^0-9]/', '', $password);
            if ($noHpDigit && ($password === $user->no_hp || $passwordDigit === $noHpDigit)) {
                $errors[] = 'Password tidak boleh sama dengan nomor HP.';
            }
        }

        if ($currentHash && Hash::check($password, $currentHash)) {
            $errors[] = 'Password baru tidak boleh sama dengan password lama.';
        }

        return $errors;
    }

    /**
     * Generate password acak yang memenuhi policy.
     */
    public function generate(int $length = 12): string
    {
        $length = max($length, self::MIN_LENGTH);

        // Loop sampai dapat string yang lolos validate() (umumnya iterasi pertama).
        for ($i = 0; $i < 10; $i++) {
            $upper   = Str::upper(Str::random(2));
            $lower   = Str::lower(Str::random(2));
            $digits  = (string) random_int(10, 99);
            $symbols = $this->randomSymbols(2);
            $extra   = Str::random(max(0, $length - 8));

            $raw = $upper . $lower . $digits . $symbols . $extra;
            $shuffled = str_shuffle($raw);

            if ($this->validate($shuffled) === []) {
                return $shuffled;
            }
        }

        // Fallback yang dijamin valid.
        return 'Aa1!' . Str::random($length - 4);
    }

    /**
     * Daftar kriteria untuk ditampilkan ke UI (label indikator).
     */
    public function rules(): array
    {
        return [
            'Minimal ' . self::MIN_LENGTH . ' karakter',
            '1 huruf besar (A-Z)',
            '1 huruf kecil (a-z)',
            '1 angka (0-9)',
            '1 simbol (!@#$%^&*)',
        ];
    }

    protected function randomSymbols(int $n): string
    {
        $out = '';
        $len = strlen(self::SYMBOLS);
        for ($i = 0; $i < $n; $i++) {
            $out .= self::SYMBOLS[random_int(0, $len - 1)];
        }
        return $out;
    }
}
