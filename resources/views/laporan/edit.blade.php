@extends('layouts.app')

@section('title', 'Ubah Status Laporan')

@section('content')
<x-page-header title="Ubah Status Laporan" subtitle="Perbarui status untuk memberi tahu warga." />

<x-flash />

<x-card class="max-w-2xl">
    <div class="space-y-2 pb-4 mb-4 border-b border-zinc-200">
        <h3 class="font-semibold text-zinc-900">{{ $laporan->judul }}</h3>
        <p class="text-sm text-zinc-500">
            {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d M Y') }} ·
            {{ \Carbon\Carbon::parse($laporan->jam)->format('H:i') }} WIB ·
            {{ $laporan->lokasi }}
        </p>
        @if($laporan->deskripsi)
            <p class="text-sm text-zinc-700">{{ $laporan->deskripsi }}</p>
        @endif
        @if($laporan->foto)
            <img src="{{ asset('storage/'.$laporan->foto) }}" alt="" class="mt-2 max-h-48 max-w-full h-auto rounded-lg border border-zinc-200">
        @endif
    </div>

    <form action="{{ route('laporan.update', $laporan) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <fieldset class="space-y-3">
            <legend class="text-sm font-semibold text-zinc-800">Status</legend>
            @php
                $opts = [
                    'baru'     => ['Baru', 'Belum ditinjau', 'warning'],
                    'diproses' => ['Diproses', 'Sedang ditangani', 'info'],
                    'selesai'  => ['Selesai', 'Sudah ditangani', 'success'],
                ];
            @endphp
            @foreach($opts as $v => [$label, $hint, $tone])
                <label class="flex items-start gap-3 p-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 cursor-pointer">
                    <input type="radio" name="status" value="{{ $v }}" @checked($laporan->status === $v)
                           class="mt-1 w-4 h-4 text-brand-700 focus:ring-brand-500">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-zinc-900">{{ $label }}</span>
                            <x-badge :variant="$tone">{{ $hint }}</x-badge>
                        </div>
                    </div>
                </label>
            @endforeach
        </fieldset>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('laporan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Status</x-button>
        </div>
    </form>
</x-card>
@endsection
