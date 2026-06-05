@extends('layouts.guest')

@section('title', 'Verifikasi Kode OTP')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Verifikasi Kode</h1>
        <p class="text-sm text-zinc-500 mt-1">Masukkan kode 6 digit yang telah dikirim ke {{ $maskedIdentifier }}.</p>
    </div>

    <x-flash />

    <form action="{{ route('password.verify-otp.submit') }}" method="POST" class="space-y-4">
        @csrf
        
        <x-input name="otp"
                 type="text"
                 inputmode="numeric"
                 label="Kode Verifikasi"
                 placeholder="000000"
                 hint="Periksa email Anda dan masukkan kode 6 digit."
                 required
                 maxlength="6"
                 pattern="[0-9]{6}"
                 autocomplete="one-time-code"
                 autofocus />

        <p class="text-xs text-zinc-500">
            Kode akan berlaku selama 10 menit.
        </p>

        <x-button type="submit" variant="primary" size="lg" block>Verifikasi Kode</x-button>
    </form>

    <div class="mt-6 space-y-2 text-sm">
        <p class="text-zinc-600">
            Tidak menerima kode?
            <a href="{{ route('password.forgot.show') }}" class="font-semibold text-brand-700 hover:text-brand-800">Minta kode baru</a>
        </p>
        <p class="text-zinc-600">
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Kembali ke halaman login</a>
        </p>
    </div>
</x-card>
@endsection
