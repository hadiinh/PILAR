@extends('layouts.guest')

@section('title', 'Lupa Kata Sandi')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Lupa Kata Sandi</h1>
        <p class="text-sm text-zinc-500 mt-1">Masukkan NIK atau email untuk menerima kode verifikasi.</p>
    </div>

    <x-flash />

    <form action="{{ route('password.forgot.request') }}" method="POST" class="space-y-4">
        @csrf
        
        <x-input name="identifier"
                 type="text"
                 label="NIK atau Email"
                 placeholder="Masukkan NIK 16 digit atau email Anda"
                 hint="Gunakan NIK yang terdaftar di RW atau alamat email yang Anda daftarkan."
                 required
                 autocomplete="username"
                 autofocus />

        <x-button type="submit" variant="primary" size="lg" block>Kirim Kode Verifikasi</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Kembali ke halaman login</a>
    </p>
</x-card>
@endsection
