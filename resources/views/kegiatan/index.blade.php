@extends('layouts.app')

@section('title', 'Kegiatan RW')

@section('content')
@php $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']); @endphp

<x-page-header title="Kegiatan RW"
               subtitle="Dokumentasi & rencana kegiatan warga RW 016.">
    <x-button href="{{ route('kegiatan.create') }}" variant="primary" icon="plus">Buat Kegiatan</x-button>
</x-page-header>

<x-flash />

@if($kegiatans->isEmpty())
    <x-card>
        <x-empty-state icon="megaphone"
                       title="Belum ada kegiatan"
                       description="Mulai catatkan kegiatan pertama untuk warga RW.">
            <x-button href="{{ route('kegiatan.create') }}" variant="primary" icon="plus">Buat Kegiatan</x-button>
        </x-empty-state>
    </x-card>
@else
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($kegiatans as $k)
    @php
        $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$k->status ?? 'baru'] ?? 'neutral';
    @endphp
    <article class="bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="aspect-[16/10] bg-zinc-100 overflow-hidden">
            @if($k->gambar)
                <img src="{{ asset('storage/'.$k->gambar) }}" alt="{{ $k->judul }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-zinc-400">
                    <x-icon name="image" class="w-10 h-10" />
                </div>
            @endif
        </div>
        <div class="p-5 flex flex-col flex-1">
            <div class="flex items-start justify-between gap-2 mb-2">
                <h3 class="font-semibold text-zinc-900 line-clamp-2">{{ $k->judul }}</h3>
                <x-badge :variant="$tone">{{ ucfirst($k->status ?? 'baru') }}</x-badge>
            </div>
            <p class="text-sm text-zinc-600 line-clamp-3 mb-3">{{ $k->deskripsi }}</p>
            <p class="text-xs text-zinc-500 mt-auto">
                <x-icon name="calendar" class="inline w-3.5 h-3.5 -mt-0.5" />
                {{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}
            </p>

            @if($isManager)
                <div class="mt-4 pt-4 border-t border-zinc-100 flex gap-2">
                    <x-button href="{{ route('kegiatan.edit', $k) }}" variant="secondary" size="sm" icon="edit" class="flex-1">Edit</x-button>
                    <form action="{{ route('kegiatan.destroy', $k) }}" method="POST"
                          class="flex-1"
                          onsubmit="return confirm('Hapus kegiatan ini?');">
                        @csrf @method('DELETE')
                        <x-button type="submit" variant="danger" size="sm" icon="trash" block>Hapus</x-button>
                    </form>
                </div>
            @endif
        </div>
    </article>
    @endforeach
</div>
@endif

@endsection
