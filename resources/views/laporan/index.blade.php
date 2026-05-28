@extends('layouts.app')

@section('title', 'Laporan Warga')

@section('content')
@php $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']); @endphp

<x-page-header title="{{ $isManager ? 'Laporan Warga' : 'Laporan Saya' }}"
               subtitle="{{ $isManager ? 'Daftar laporan masalah dari warga.' : 'Sampaikan masalah lingkungan kepada pengurus RW.' }}">
    <x-button href="{{ route('laporan.create') }}" variant="primary" icon="plus">Buat Laporan</x-button>
</x-page-header>

<x-flash />

@if(session('laporan_success'))
    <x-alert variant="success" class="mb-4">{{ session('laporan_success') }}</x-alert>
@endif

<div class="grid grid-cols-3 gap-2 sm:gap-3 mb-5">

    {{-- Baru --}}
    <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-sm min-h-[110px] flex flex-col items-center justify-center text-center gap-2">
        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
            <x-icon name="alert" class="w-5 h-5" />
        </div>

        <p class="text-2xl font-bold text-zinc-900">
            {{ $countBaru ?? 0 }}
        </p>

        <p class="text-xs font-semibold text-zinc-500">
            Baru
        </p>
    </div>

    {{-- Diproses --}}
    <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-sm min-h-[110px] flex flex-col items-center justify-center text-center gap-2">
        <div class="w-10 h-10 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
            <x-icon name="clock" class="w-5 h-5" />
        </div>

        <p class="text-2xl font-bold text-zinc-900">
            {{ $countDiproses ?? 0 }}
        </p>

        <p class="text-xs font-semibold text-zinc-500">
            Diproses
        </p>
    </div>

    {{-- Selesai --}}
    <div class="bg-white border border-zinc-200 rounded-2xl p-4 shadow-sm min-h-[110px] flex flex-col items-center justify-center text-center gap-2">
        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
            <x-icon name="check-circle" class="w-5 h-5" />
        </div>

        <p class="text-2xl font-bold text-zinc-900">
            {{ $countSelesai ?? 0 }}
        </p>

        <p class="text-xs font-semibold text-zinc-500">
            Selesai
        </p>
    </div>

</div>

<x-card>
    <div class="flex gap-2 overflow-x-auto no-scrollbar mb-4">
        @php
            $tabs = ['semua' => 'Semua', 'baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
        @endphp
        @foreach($tabs as $k => $label)
            <button type="button" data-filter="{{ $k }}"
                    class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold
                           {{ $k === 'semua' ? 'bg-brand-700 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    @if($laporans->isEmpty())
        <x-empty-state icon="flag"
                       title="Belum ada laporan"
                       description="Laporan yang Anda kirim akan muncul di sini.">
            <x-button href="{{ route('laporan.create') }}" variant="primary" icon="plus">Buat Laporan Pertama</x-button>
        </x-empty-state>
    @else
    <ul class="divide-y divide-zinc-100">
        @foreach($laporans as $l)
            @php
                // Warga hanya melihat laporannya sendiri (sudah difilter di controller),
                // tapi sembunyikan laporan "baru" warga lain di tampilan admin tidak relevan.
                $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$l->status] ?? 'neutral';
            @endphp
            <li class="laporan-row py-3 flex items-start gap-3" data-status="{{ $l->status }}">
                <span class="w-9 h-9 shrink-0 rounded-full flex items-center justify-center
                            {{ ['baru' => 'bg-amber-100 text-amber-700', 'diproses' => 'bg-sky-100 text-sky-700', 'selesai' => 'bg-emerald-100 text-emerald-700'][$l->status] ?? 'bg-zinc-100 text-zinc-700' }}">
                    <x-icon name="flag" class="w-4 h-4" />
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-zinc-900 truncate">{{ $l->judul }}</p>
                        <x-badge :variant="$tone">{{ ucfirst($l->status) }}</x-badge>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}
                        @if($isManager && $l->user)
                            · oleh {{ $l->user->name }}
                        @endif
                        @if($l->deskripsi) · {{ Str::limit($l->deskripsi, 80) }} @endif
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-1.5">
                    <x-button href="{{ route('laporan.show', $l) }}" variant="ghost" size="sm">Detail</x-button>
                    @if($isManager)
                        <x-button href="{{ route('laporan.edit', $l) }}" variant="secondary" size="sm" icon="settings">Status</x-button>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
    @endif
</x-card>

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

        document.querySelectorAll('.laporan-row').forEach(r => {
            r.style.display = (f === 'semua' || r.dataset.status === f) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
