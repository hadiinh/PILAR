@extends('layouts.app')

@section('title', 'Kegiatan RW')

@section('content')
@php
    $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']);
    $kategori = $kegiatans->pluck('kategori')->filter()->unique()->values();
    $statusLabels = \App\Models\Kegiatan::statusLabels();
    $statusTones = \App\Models\Kegiatan::statusTones();
@endphp

<x-page-header title="Kegiatan RW"
               subtitle="Dokumentasi & rencana kegiatan warga RW 016.">
    @if($isManager)
        <x-button href="{{ route('kegiatan.create') }}" variant="primary" icon="plus">Buat Kegiatan</x-button>
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

@if($kegiatans->isEmpty())
    <x-card>
        <x-empty-state icon="megaphone"
                       title="Belum ada kegiatan"
                       description="{{ $isManager ? 'Mulai catatkan kegiatan pertama untuk warga RW.' : 'Belum ada kegiatan yang dipublikasikan pengurus RW.' }}">
            @if($isManager)
                <x-button href="{{ route('kegiatan.create') }}" variant="primary" icon="plus">Buat Kegiatan</x-button>
            @endif
        </x-empty-state>
    </x-card>
@else
<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @foreach($kegiatans as $k)
    @php
        $status = $k->status;
        $statusLabel = $statusLabels[$status] ?? ucfirst($status);
        $tone = $statusTones[$status] ?? 'neutral';
    @endphp
    <article class="kegiatan-item bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col"
             data-kategori="{{ strtolower($k->kategori ?? 'semua') }}">
        <a href="{{ route('kegiatan.show', $k) }}" class="group block flex-1 min-w-0">
            <div class="aspect-square bg-zinc-100 overflow-hidden">
                @if($k->gambar)
                    <img src="{{ asset('storage/'.$k->gambar) }}" alt="{{ $k->judul }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-zinc-400">
                        <x-icon name="image" class="w-10 h-10" />
                    </div>
                @endif
            </div>
            <div class="p-3">
                <p class="text-sm font-semibold text-zinc-900 truncate">{{ $k->judul }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <x-badge :variant="$tone">{{ $statusLabel }}</x-badge>
                    <span class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}</span>
                </div>
            </div>
        </a>

        
    </article>
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

        document.querySelectorAll('.kegiatan-item').forEach(item => {
            item.style.display = (f === 'semua' || item.dataset.kategori === f) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
