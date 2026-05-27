@extends('layouts.app')

@section('title', 'Foto Kegiatan')

@section('content')
@php
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
    $kategori = $fotos->pluck('kategori')->filter()->unique()->values();
@endphp

<x-page-header title="Foto Kegiatan"
               subtitle="Dokumentasi kegiatan warga RW 016.">
    @if($isManager)
        <x-button href="{{ route('foto.create') }}" variant="primary" icon="plus">Unggah Foto</x-button>
    @endif
</x-page-header>

<x-flash />

<div class="flex gap-2 overflow-x-auto no-scrollbar mb-4">
    <button type="button" data-filter="semua"
            class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold bg-brand-700 text-white">
        Semua
    </button>
    @foreach($kategori as $kat)
        <button type="button" data-filter="{{ strtolower($kat) }}"
                class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50">
            {{ $kat }}
        </button>
    @endforeach
</div>

@if($fotos->isEmpty())
    <x-card>
        <x-empty-state icon="image"
                       title="Belum ada foto"
                       description="Foto kegiatan warga akan ditampilkan di sini.">
            @if($isManager)
                <x-button href="{{ route('foto.create') }}" variant="primary" icon="plus">Unggah Sekarang</x-button>
            @endif
        </x-empty-state>
    </x-card>
@else
<div id="fotoGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
    @foreach($fotos as $foto)
        <a href="{{ route('foto.show', $foto) }}"
           class="foto-item group block bg-white rounded-xl border border-zinc-200 overflow-hidden hover:shadow-md transition-shadow"
           data-kategori="{{ strtolower($foto->kategori ?? 'semua') }}">
            <div class="aspect-square bg-zinc-100 overflow-hidden">
                <img src="{{ asset('storage/'.$foto->gambar) }}" alt="{{ $foto->judul }}"
                     class="w-full h-full object-cover">
            </div>
            <div class="p-3">
                <p class="text-sm font-semibold text-zinc-900 truncate">{{ $foto->judul }}</p>
                <div class="flex items-center gap-2 mt-1">
                    @if($foto->kategori)<x-badge variant="brand">{{ $foto->kategori }}</x-badge>@endif
                    <span class="text-xs text-zinc-500">{{ $foto->created_at->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </a>
    @endforeach
</div>
@endif

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const f = this.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-brand-700', 'text-white');
            b.classList.add('bg-white', 'text-zinc-700', 'border', 'border-zinc-200', 'hover:bg-zinc-50');
        });
        this.classList.remove('bg-white', 'text-zinc-700', 'border', 'border-zinc-200', 'hover:bg-zinc-50');
        this.classList.add('bg-brand-700', 'text-white');

        document.querySelectorAll('.foto-item').forEach(item => {
            item.style.display = (f === 'semua' || item.dataset.kategori === f) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
