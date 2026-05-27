@extends('layouts.app')

@section('title', 'Tambah Kegiatan')

@section('content')
<x-page-header title="Tambah Kegiatan"
               subtitle="Lengkapi detail kegiatan RW dengan jelas." />

<x-flash />

<x-card>
    <form action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
        @csrf

        <x-input name="judul" label="Judul Kegiatan" placeholder="Contoh: Gotong Royong RT 04" required />

        <x-textarea name="deskripsi" label="Deskripsi" rows="5"
                    placeholder="Tuliskan tujuan, waktu, dan informasi penting lainnya." required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-input name="tanggal" type="date" label="Tanggal Kegiatan" required />
            <x-select name="status" label="Status">
                <option value="baru" {{ old('status') === 'baru' ? 'selected' : '' }}>Baru</option>
                <option value="diproses" {{ old('status') === 'diproses' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
            </x-select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-zinc-800 mb-1.5">Foto Pendukung (opsional)</label>
            <input type="file" name="gambar" accept="image/*"
                   class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-zinc-200">
            <p class="text-xs text-zinc-500 mt-1.5">Format JPG/PNG/WebP. Maksimal 2 MB.</p>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('kegiatan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Kegiatan</x-button>
        </div>
    </form>
</x-card>
@endsection
