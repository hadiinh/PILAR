@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<x-card padding="p-6 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-zinc-900">Daftar Akun Baru</h1>
        <p class="text-sm text-zinc-500 mt-1">Lengkapi data berikut untuk mengakses layanan RW 016.</p>
    </div>

    <x-flash />

    <form action="{{ url('/register') }}" method="POST" class="space-y-5">
        @csrf

        {{-- Identitas --}}
        <div class="space-y-4">
            <div class="pb-1 border-b border-zinc-200">
                <h3 class="text-sm font-semibold text-zinc-900">Identitas Akun</h3>
                <p class="text-xs text-zinc-500">Informasi login dan kontak utama.</p>
            </div>

            <x-input name="name"
                     label="Nama Lengkap"
                     placeholder="Contoh: Budi Santoso"
                     required
                     autocomplete="name"
                     autofocus />
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input name="email"
                         type="email"
                         label="Email"
                         placeholder="contoh@email.com"
                         hint="Email digunakan untuk masuk."
                         required
                         autocomplete="email" />
                <x-input name="no_hp"
                         type="tel"
                         label="Nomor HP / WhatsApp"
                         placeholder="08123456789"
                         hint="Digunakan untuk notifikasi WhatsApp."
                         required
                         autocomplete="tel" />
            </div>

            <div class="space-y-1.5">
                <label for="status_warga" class="block text-sm font-semibold text-zinc-800">Status Warga</label>
                <select id="status_warga" name="status_warga"
                        class="block w-full h-11 px-3.5 rounded-lg border bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                    @foreach(['tetap' => 'Warga Tetap', 'kontrak' => 'Kontrak', 'kos' => 'Kos', 'lainnya' => 'Lainnya'] as $v => $label)
                        <option value="{{ $v }}" @selected(old('status_warga','tetap') === $v)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <x-input name="password"
                         type="password"
                         label="Kata Sandi"
                         placeholder="Contoh: MyPassword@123"
                         hint="Min 8 karakter, 1 besar, 1 kecil, 1 angka, 1 simbol"
                         required
                         autocomplete="new-password" />
                <x-input name="password_confirmation"
                         type="password"
                         label="Ulangi Kata Sandi"
                         placeholder="Tulis ulang kata sandi"
                         required
                         autocomplete="new-password" />
            </div>
        </div>

        {{-- Alamat --}}
        @include('partials.alamat-fields', ['user' => null, 'showHeader' => true])

        {{-- reCAPTCHA v2 Checkbox --}}
        <div class="flex justify-center py-2">
            <div class="g-recaptcha" data-sitekey="{{ $recaptchaPublicKey ?? '' }}"></div>
        </div>
        @error('g-recaptcha-response')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror

        <x-button type="submit" variant="primary" size="lg" block>Daftar</x-button>
    </form>

    <p class="text-center text-sm text-zinc-600 mt-6">
        Sudah punya akun?
        <a href="{{ url('/login') }}" class="font-semibold text-brand-700 hover:text-brand-800">Masuk di sini</a>
    </p>
</x-card>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@vite('resources/js/wilayah.js')
@endpush
@endsection