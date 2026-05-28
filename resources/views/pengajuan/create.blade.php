@extends('layouts.guest')

@section('title', 'Pengajuan Akun')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Pengajuan Akun Warga</h1>
        <p class="text-sm text-zinc-500 mt-1">Daftarkan akses Anda ke layanan PILAR RW 016.</p>
    </div>

    <x-flash />

    <div class="mb-5 p-3 rounded-lg bg-zinc-50 border border-zinc-200 text-xs text-zinc-600">
        <p class="font-semibold text-zinc-800 mb-1">Cara mengajukan akun</p>
        <ol class="list-decimal pl-4 space-y-0.5">
            <li>Pastikan NIK Anda sudah terdaftar sebagai warga RW.</li>
            <li>Isi nomor HP/WhatsApp aktif untuk menerima notifikasi.</li>
            <li>Tunggu persetujuan dari pengurus RW.</li>
            <li>Setelah disetujui, kata sandi akan dikirim via WhatsApp.</li>
        </ol>
    </div>

    <form action="{{ route('pengajuan.store') }}" method="POST" class="space-y-4">
        @csrf
        <x-input name="nik"
                 type="text"
                 inputmode="numeric"
                 label="NIK"
                 placeholder="16 digit NIK"
                 maxlength="16"
                 pattern="[0-9]{16}"
                 required
                 autocomplete="off"
                 autofocus />

        <x-input name="no_hp"
                 type="tel"
                 label="Nomor HP / WhatsApp Aktif"
                 placeholder="08123456789"
                 hint="Pastikan nomor terdaftar di WhatsApp."
                 required />

        <x-input name="password"
                 type="password"
                 label="Kata Sandi yang Diinginkan"
                 placeholder="Minimal 6 karakter"
                 required
                 autocomplete="new-password" />

        <x-input name="password_confirmation"
                 type="password"
                 label="Ulangi Kata Sandi"
                 placeholder="Tulis ulang kata sandi"
                 required
                 autocomplete="new-password" />

        <x-button type="submit" variant="primary" size="lg" block>Kirim Pengajuan</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Masuk di sini</a>
    </p>
</x-card>
@endsection
