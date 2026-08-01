@extends('layouts.app')

@section('title', 'Edit Kegiatan')

@section('content')
<x-page-header title="Edit Kegiatan" subtitle="Perbarui detail kegiatan." />

<x-flash />

<x-card>
    <form action="{{ route('kegiatan.update', $kegiatan) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
        @csrf @method('PUT')

        <x-input name="judul" label="Judul Kegiatan" :value="$kegiatan->judul" required />
        <x-textarea name="deskripsi" label="Deskripsi" rows="5" :value="$kegiatan->deskripsi" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-input name="tanggal" type="date" label="Tanggal" :value="$kegiatan->tanggal" required />
            <x-select name="kategori" label="Kategori">
                <option value="">— Pilih kategori —</option>
                @foreach(['Sosial', 'Kebersihan', 'Rapat', 'Olahraga', 'Lainnya'] as $kat)
                    <option value="{{ $kat }}" @selected($kegiatan->kategori === $kat)>{{ $kat }}</option>
                @endforeach
            </x-select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-zinc-800 mb-1.5">Ganti Foto (opsional)</label>
            <input type="file" name="gambar" accept="image/*"
                   class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-zinc-200">
            @if($kegiatan->gambar)
                <p class="text-xs text-zinc-500 mt-2">Foto saat ini:</p>
                <img src="{{ asset('storage/'.$kegiatan->gambar) }}" alt="" class="mt-1 h-24 rounded-lg border border-zinc-200">
            @endif
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('kegiatan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>
@endsection
