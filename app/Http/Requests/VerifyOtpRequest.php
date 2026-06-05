<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => 'required|string|digits:6',
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'Kode verifikasi wajib diisi.',
            'otp.digits'   => 'Kode verifikasi harus 6 angka.',
        ];
    }

    public function get(string $key, $default = null): mixed
    {
        if ($key === 'otp') {
            return trim((string) parent::get('otp', $default));
        }
        return parent::get($key, $default);
    }
}
