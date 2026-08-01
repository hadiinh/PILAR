@extends('layouts.app')

@section('title', $laporan->judul)

@section('content')
@php
    $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$laporan->status] ?? 'neutral';
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
@endphp

<x-page-header :title="$laporan->judul">
    <x-button href="{{ route('laporan.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
    @if($isManager)
        <x-button href="{{ route('laporan.edit', $laporan) }}" variant="primary" icon="settings">Ubah Status</x-button>
    @endif
</x-page-header>

<x-card>
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <x-badge :variant="$tone">{{ ucfirst($laporan->status) }}</x-badge>
        @if($laporan->user)
            <span class="text-sm text-zinc-500">dilaporkan oleh <strong class="text-zinc-700">{{ $laporan->user->name }}</strong></span>
        @endif
    </div>

    <div class="grid sm:grid-cols-3 gap-3 mb-5">
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Tanggal</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('d M Y') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Jam</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ \Carbon\Carbon::parse($laporan->jam)->format('H:i') }} WIB</p>
        </div>
        <div class="p-3 rounded-lg bg-zinc-50 border border-zinc-200">
            <p class="text-xs uppercase tracking-wide text-zinc-500">Lokasi</p>
            <p class="font-semibold text-zinc-900 mt-1">{{ $laporan->lokasi }}</p>
        </div>
    </div>

    @if($laporan->deskripsi)
        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-1">Deskripsi</h4>
        <p class="text-zinc-700 whitespace-pre-line wrap-anywhere max-w-full mb-4">{{ $laporan->deskripsi }}</p>
    @endif

    @if($laporan->foto)
        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-1">Foto Bukti</h4>
        <img src="{{ asset('storage/'.$laporan->foto) }}" alt="" class="max-h-96 max-w-full h-auto rounded-lg border border-zinc-200">
    @endif
</x-card>
@endsection
