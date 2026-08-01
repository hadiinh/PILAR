@extends('layouts.app')

@section('title', $jadwal->judul)

@section('content')
@php
    $tgl = \Carbon\Carbon::parse($jadwal->tanggal);
    $today = \Carbon\Carbon::today();
    $auto = $tgl->gt($today) ? 'akan-datang' : ($tgl->isSameDay($today) ? 'berlangsung' : 'selesai');
    $tone = ['akan-datang' => 'info', 'berlangsung' => 'success', 'selesai' => 'neutral'][$auto];
    $label = ['akan-datang' => 'Akan Datang', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai'][$auto];
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
@endphp

<x-page-header :title="$jadwal->judul">
    <x-button href="{{ route('jadwal.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
    @if($isManager)
        <x-button href="{{ route('jadwal.edit', $jadwal) }}" variant="primary" icon="edit">Edit</x-button>
    @endif
</x-page-header>

<x-card>
    <div class="flex flex-wrap gap-2 mb-4">
        <x-badge :variant="$tone">{{ $label }}</x-badge>
        @if($jadwal->kategori)
            <x-badge variant="brand">{{ $jadwal->kategori }}</x-badge>
        @endif
    </div>

    <div class="grid sm:grid-cols-3 gap-3 mb-5">
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Tanggal</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ $tgl->translatedFormat('d M Y') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Waktu</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ \Carbon\Carbon::parse($jadwal->jam)->format('H:i') }} WIB</p>
        </div>
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Lokasi</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ $jadwal->lokasi }}</p>
        </div>
    </div>

    <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-2">Deskripsi</h4>
    <p class="text-zinc-700 leading-relaxed whitespace-pre-line break-words">{{ $jadwal->deskripsi ?: '—' }}</p>
</x-card>
@endsection
