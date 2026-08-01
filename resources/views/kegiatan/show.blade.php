@extends('layouts.app')

@section('title', $kegiatan->judul)

@section('content')
@php
    $status = $kegiatan->status;
    $statusLabel = \App\Models\Kegiatan::statusLabels()[$status] ?? ucfirst($status);
    $tone = \App\Models\Kegiatan::statusTones()[$status] ?? 'neutral';
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
@endphp

<x-page-header :title="$kegiatan->judul" :subtitle="$statusLabel">
    <x-button href="{{ route('kegiatan.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
    @if($kegiatan->gambar)
        <x-button :href="asset('storage/'.$kegiatan->gambar)" variant="primary" icon="download" download>Unduh</x-button>
    @endif
    @if($isManager)
        <x-button href="{{ route('kegiatan.edit', $kegiatan) }}" variant="secondary" icon="edit">Edit</x-button>
    @endif
</x-page-header>

<x-card>
    @if($kegiatan->gambar)
        <div class="bg-zinc-100 rounded-xl overflow-hidden flex items-center justify-center mb-4">
            <img src="{{ asset('storage/'.$kegiatan->gambar) }}" alt="{{ $kegiatan->judul }}"
                 class="max-h-[70vh] w-auto object-contain">
        </div>
    @endif

    <div class="flex flex-wrap gap-2 mb-4">
        <x-badge :variant="$tone">{{ $statusLabel }}</x-badge>
        @if($kegiatan->kategori)
            <x-badge variant="brand">{{ $kegiatan->kategori }}</x-badge>
        @endif
        <x-badge variant="neutral">
            <x-icon name="calendar" class="w-3.5 h-3.5" />
            {{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('d M Y') }}
        </x-badge>
    </div>

    @if($kegiatan->deskripsi)
        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-2">Deskripsi</h4>
        <p class="text-zinc-700 leading-relaxed whitespace-pre-line wrap-anywhere">{{ $kegiatan->deskripsi }}</p>
    @endif

    @if($isManager)
    <form action="{{ route('kegiatan.destroy', $kegiatan) }}" method="POST"
          class="mt-4 pt-4 border-t border-zinc-200"
          onsubmit="return confirm('Hapus kegiatan ini secara permanen?');">
        @csrf @method('DELETE')
        <x-button type="submit" variant="danger" icon="trash">Hapus Kegiatan</x-button>
    </form>
    @endif
</x-card>
@endsection
