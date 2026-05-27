@extends('layouts.app')

@section('title', $kegiatan->judul)

@section('content')
@php
    $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$kegiatan->status ?? 'baru'] ?? 'neutral';
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
@endphp

<x-page-header :title="$kegiatan->judul">
    <x-button href="{{ route('kegiatan.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
    @if($isManager)
        <x-button href="{{ route('kegiatan.edit', $kegiatan) }}" variant="primary" icon="edit">Edit</x-button>
    @endif
</x-page-header>

<x-card>
    @if($kegiatan->gambar)
        <div class="aspect-[16/8] rounded-xl bg-zinc-100 overflow-hidden mb-4">
            <img src="{{ asset('storage/'.$kegiatan->gambar) }}" alt="{{ $kegiatan->judul }}" class="w-full h-full object-cover">
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        <x-badge :variant="$tone">{{ ucfirst($kegiatan->status ?? 'baru') }}</x-badge>
        <x-badge variant="neutral">
            <x-icon name="calendar" class="w-3.5 h-3.5" />
            {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d M Y') }}
        </x-badge>
    </div>

    <p class="text-zinc-700 leading-relaxed whitespace-pre-line">{{ $kegiatan->deskripsi }}</p>
</x-card>
@endsection
