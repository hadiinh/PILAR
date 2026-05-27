@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Daftar Akun Baru</h1>
        <p class="text-sm text-zinc-500 mt-1">Buat akun untuk mengakses layanan RW 016.</p>
    </div>

    <x-flash />

    <form action="{{ url('/register') }}" method="POST" class="space-y-4">
        @csrf
        <x-input name="name"
                 label="Nama Lengkap"
                 placeholder="Contoh: Budi Santoso"
                 required
                 autocomplete="name"
                 autofocus />
        <x-input name="email"
                 type="email"
                 label="Email"
                 placeholder="contoh@email.com"
                 hint="Email digunakan untuk masuk."
                 required
                 autocomplete="email" />
        <x-input name="password"
                 type="password"
                 label="Kata Sandi"
                 placeholder="Minimal 6 karakter"
                 hint="Gunakan kombinasi yang mudah Anda ingat."
                 required
                 autocomplete="new-password" />
        <x-input name="password_confirmation"
                 type="password"
                 label="Ulangi Kata Sandi"
                 placeholder="Tulis ulang kata sandi"
                 required
                 autocomplete="new-password" />

        <x-button type="submit" variant="primary" size="lg" block>Daftar</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        Sudah punya akun?
        <a href="{{ url('/login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Masuk di sini</a>
    </p>
</x-card>
@endsection
