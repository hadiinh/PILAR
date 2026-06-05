<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'NIK atau email wajib diisi.',
        ];
    }

    public function get(string $key, $default = null): mixed
    {
        if ($key === 'identifier') {
            return trim((string) parent::get('identifier', $default));
        }
        return parent::get($key, $default);
    }
}
