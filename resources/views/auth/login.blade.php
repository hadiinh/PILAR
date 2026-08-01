@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Selamat datang</h1>
        <p class="text-sm text-zinc-500 mt-1">Masuk dengan NIK Anda untuk melanjutkan ke PILAR RW 016.</p>
    </div>

    <x-flash />

    <form action="{{ url('/login') }}" method="POST" class="space-y-4">
        @csrf
        <x-input name="nik"
                 type="text"
                 inputmode="numeric"
                 label="NIK"
                 placeholder="16 digit NIK"
                 hint="Gunakan NIK yang terdaftar di RW."
                 required
                 maxlength="16"
                 pattern="[0-9]{16}"
                 autocomplete="username"
                 autofocus />
        <x-input name="password"
                 type="password"
                 label="Kata Sandi"
                 placeholder="Masukkan kata sandi"
                 required
                 autocomplete="current-password" />

        {{-- reCAPTCHA v2 Checkbox --}}
        <div class="flex justify-center py-2">
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaPublicKey }}"></div>
        </div>
        @error('g-recaptcha-response')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <label class="flex items-center gap-2 text-sm text-zinc-600">
            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-zinc-300 text-brand-700 focus:ring-brand-500">
            Ingat saya di perangkat ini
        </label>

        <div class="text-right">
            <a href="{{ route('password.forgot.show') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lupa Kata Sandi?</a>
        </div>

        <x-button type="submit" variant="primary" size="lg" block>Masuk</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        Belum punya akun?
        <a href="{{ url('/pengajuan-akun') }}" class="font-semibold text-brand-700 hover:text-brand-800">Ajukan akun di sini</a>
    </p>

    @env('local')
        <div class="mt-6 border-t border-zinc-200 pt-4">
            <p class="text-xs font-semibold text-zinc-700 mb-2">Akun demo (mode lokal):</p>
            <ul class="text-xs text-zinc-600 space-y-1">
                <li><span class="font-semibold">Ketua RW:</span> 3277010003000001</li>
                <li><span class="font-semibold">Admin:</span> 3277010003000002</li>
                <li><span class="font-semibold">Warga:</span> 3277010003001001</li>
                <li><span class="font-semibold">Kata sandi:</span> password</li>
            </ul>
        </div>
    @endenv
</x-card>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
@endsection