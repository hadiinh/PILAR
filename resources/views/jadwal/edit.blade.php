@extends('layouts.app')

@section('title', 'Edit Jadwal')

@section('content')
<x-page-header title="Edit Jadwal" subtitle="Perbarui detail kegiatan." />

<x-flash />

<x-card>
    <form action="{{ route('jadwal.update', $jadwal) }}" method="POST" class="space-y-5 max-w-2xl">
        @csrf @method('PUT')

        <x-input name="judul" label="Nama Kegiatan" :value="$jadwal->judul" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-input name="tanggal" type="date" label="Tanggal" :value="$jadwal->tanggal" required />
            <x-input name="jam" type="time" label="Waktu Mulai" :value="$jadwal->jam" required />
        </div>

        <x-input name="lokasi" label="Lokasi" :value="$jadwal->lokasi" required />

        <x-select name="kategori" label="Kategori">
            <option value="">— Pilih kategori —</option>
            @foreach(['Sosial', 'Kebersihan', 'Rapat', 'Olahraga', 'Lainnya'] as $kat)
                <option value="{{ $kat }}" @selected($jadwal->kategori === $kat)>{{ $kat }}</option>
            @endforeach
        </x-select>

        <x-textarea name="deskripsi" label="Deskripsi" rows="4" :value="$jadwal->deskripsi" />

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('jadwal.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>
@endsection
