@extends('layouts.app')

@section('title', $foto->judul)

@section('content')
@php $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']); @endphp

<x-page-header :title="$foto->judul" :subtitle="$foto->kategori ? 'Kategori: ' . $foto->kategori : null">
    <x-button href="{{ route('foto.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
    <x-button :href="asset('storage/'.$foto->gambar)" variant="primary" icon="download" download>Unduh</x-button>
    @if($isManager)
        <x-button href="{{ route('foto.edit', $foto) }}" variant="secondary" icon="edit">Edit</x-button>
    @endif
</x-page-header>

<x-card>
    <div class="bg-zinc-100 rounded-xl overflow-hidden flex items-center justify-center">
        <img src="{{ asset('storage/'.$foto->gambar) }}" alt="{{ $foto->judul }}"
             class="max-h-[70vh] w-auto object-contain">
    </div>

    @if($foto->deskripsi)
        <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mt-5 mb-2">Deskripsi</h4>
        <p class="text-zinc-700 whitespace-pre-line break-words">{{ $foto->deskripsi }}</p>
    @endif

    <p class="text-xs text-zinc-500 mt-4">Diunggah {{ $foto->created_at->translatedFormat('d M Y H:i') }}</p>

    @if($isManager)
    <form action="{{ route('foto.destroy', $foto) }}" method="POST"
          class="mt-4 pt-4 border-t border-zinc-200"
          onsubmit="return confirm('Hapus foto ini secara permanen?');">
        @csrf @method('DELETE')
        <x-button type="submit" variant="danger" icon="trash">Hapus Foto</x-button>
    </form>
    @endif
</x-card>
@endsection
