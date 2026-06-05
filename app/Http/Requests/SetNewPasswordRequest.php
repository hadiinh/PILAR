<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetNewPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required'      => 'Kata sandi baru wajib diisi.',
            'password.min'           => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'     => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
        ];
    }
}
