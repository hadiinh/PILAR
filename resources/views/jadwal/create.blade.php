@extends('layouts.app')

@section('title', 'Tambah Jadwal')

@section('content')
<x-page-header title="Tambah Jadwal" subtitle="Tentukan waktu dan lokasi kegiatan." />

<x-flash />

<x-card>
    <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-5 max-w-2xl">
        @csrf

        <x-input name="judul" label="Nama Kegiatan" placeholder="Contoh: Gotong Royong" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-input name="tanggal" type="date" label="Tanggal" required />
            <x-input name="jam" type="time" label="Waktu Mulai" hint="Format 24 jam (contoh: 19:30)" required />
        </div>

        <x-input name="lokasi" label="Lokasi" placeholder="Contoh: Balai RW 016" required />

        <x-select name="kategori" label="Kategori">
            <option value="">— Pilih kategori —</option>
            @foreach(['Sosial', 'Kebersihan', 'Rapat', 'Olahraga', 'Lainnya'] as $kat)
                <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
            @endforeach
        </x-select>

        <x-textarea name="deskripsi" label="Deskripsi (opsional)" rows="4"
                    placeholder="Tuliskan rincian kegiatan agar warga lebih siap." />

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('jadwal.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Jadwal</x-button>
        </div>
    </form>
</x-card>
@endsection
