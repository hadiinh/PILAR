<?php

namespace App\Rules;

use App\Models\User;
use App\Services\PasswordPolicyService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongPassword implements ValidationRule
{
    /**
     * @param  User|null    $user        Konteks user untuk cek "tidak sama dengan NIK/no_hp".
     * @param  string|null  $currentHash Hash password lama untuk cek "tidak sama dengan password lama".
     */
    public function __construct(
        protected ?User $user = null,
        protected ?string $currentHash = null,
    ) {}

    /**
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('Password tidak valid.');
            return;
        }

        $policy = app(PasswordPolicyService::class);
        $errors = $policy->validate($value, $this->user, $this->currentHash);

        if (!empty($errors)) {
            $fail(implode("\n", $errors));
        }
    }
}
