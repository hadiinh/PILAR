<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    private $errors = [];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $this->errors = [];

        // Cek minimal 8 karakter
        if (strlen($value) < 8) {
            $this->errors[] = 'Password minimal 8 karakter';
        }

        // Cek huruf besar (A-Z)
        if (!preg_match('/[A-Z]/', $value)) {
            $this->errors[] = 'Password harus mengandung huruf besar';
        }

        // Cek huruf kecil (a-z)
        if (!preg_match('/[a-z]/', $value)) {
            $this->errors[] = 'Password harus mengandung huruf kecil';
        }

        // Cek angka (0-9)
        if (!preg_match('/[0-9]/', $value)) {
            $this->errors[] = 'Password harus mengandung angka';
        }

        // Cek karakter khusus / simbol
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'\"<>,.?\/]/', $value)) {
            $this->errors[] = 'Password harus mengandung simbol (!@#$%^&* dll)';
        }

        // Jika ada error, tampilkan semuanya
        if (!empty($this->errors)) {
            $fail(implode("\n", $this->errors));
        }
    }
}
