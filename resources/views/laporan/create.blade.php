@extends('layouts.app')

@section('title', 'Buat Laporan')

@section('content')
<x-page-header title="Buat Laporan"
               subtitle="Sampaikan masalah lingkungan kepada pengurus RW." />

<x-flash />

<x-card>
    <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
        @csrf

        <x-input name="judul" label="Judul Laporan" placeholder="Contoh: Lampu jalan padam" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-input name="tanggal" type="date" label="Tanggal Kejadian" :value="now()->toDateString()" required />
            <x-input name="jam" type="time" label="Perkiraan Jam" :value="now()->format('H:i')" required />
        </div>

        <x-input name="lokasi" label="Lokasi" placeholder="Contoh: Jl. Melati No. 12, RT 04" required />

        <x-textarea name="deskripsi" label="Deskripsi" rows="5"
                    placeholder="Ceritakan secara singkat masalah yang Anda alami." />

        <div>
            <label class="block text-sm font-semibold text-zinc-800 mb-1.5">Foto Bukti (opsional)</label>
            <input type="file" name="foto" accept="image/*"
                   class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-zinc-200">
            <p class="text-xs text-zinc-500 mt-1.5">JPG/PNG/WebP, maksimal 2 MB.</p>
        </div>

        <x-alert variant="info">
            Laporan Anda akan langsung diterima pengurus RW. Status akan diperbarui ketika sedang diproses atau telah selesai ditangani.
        </x-alert>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('laporan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Kirim Laporan</x-button>
        </div>
    </form>
</x-card>
@endsection
