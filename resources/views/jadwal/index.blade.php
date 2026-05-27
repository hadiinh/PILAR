@extends('layouts.app')

@section('title', 'Jadwal Kegiatan')

@section('content')
@php $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']); @endphp

<x-page-header title="Jadwal Kegiatan"
               subtitle="Lihat semua agenda kegiatan warga RW.">
    @if($isManager)
        <x-button href="{{ route('jadwal.create') }}" variant="primary" icon="plus">Tambah Jadwal</x-button>
    @endif
</x-page-header>

<x-flash />

{{-- Filter tabs --}}
<div class="flex gap-2 overflow-x-auto no-scrollbar mb-4" role="tablist">
    @php
        $filters = [
            'semua'       => 'Semua',
            'akan-datang' => 'Akan Datang',
            'berlangsung' => 'Berlangsung',
            'selesai'     => 'Selesai',
        ];
    @endphp
    @foreach($filters as $key => $label)
        <button type="button" data-filter="{{ $key }}"
                class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold
                       {{ $key === 'semua' ? 'bg-brand-700 text-white' : 'bg-white text-zinc-700 border border-zinc-200 hover:bg-zinc-50' }}">
            {{ $label }}
        </button>
    @endforeach
</div>

@if($jadwals->isEmpty())
    <x-card>
        <x-empty-state icon="calendar" title="Belum ada jadwal" description="Belum ada agenda yang dijadwalkan saat ini." />
    </x-card>
@else
<div class="space-y-3" id="jadwalList">
    @foreach($jadwals as $j)
        @php
            $today = \Carbon\Carbon::today();
            $tgl = \Carbon\Carbon::parse($j->tanggal);
            $auto = $tgl->gt($today) ? 'akan-datang' : ($tgl->isSameDay($today) ? 'berlangsung' : 'selesai');
            $tone = ['akan-datang' => 'info', 'berlangsung' => 'success', 'selesai' => 'neutral'][$auto];
            $label = ['akan-datang' => 'Akan Datang', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai'][$auto];
        @endphp
        <article class="jadwal-item bg-white border border-zinc-200/80 rounded-2xl shadow-sm overflow-hidden" data-status="{{ $auto }}">
            <button type="button"
                    class="w-full text-left p-4 sm:p-5 flex gap-4 items-start"
                    onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('[data-arrow]').classList.toggle('rotate-180');">
                <div class="w-14 shrink-0 text-center bg-brand-50 text-brand-800 rounded-lg p-2">
                    <p class="text-[10px] font-semibold uppercase">{{ $tgl->translatedFormat('M') }}</p>
                    <p class="text-xl font-bold leading-none">{{ $tgl->format('d') }}</p>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h3 class="font-semibold text-zinc-900 truncate">{{ $j->judul }}</h3>
                        <x-badge :variant="$tone">{{ $label }}</x-badge>
                        @if($j->kategori)
                            <x-badge variant="brand">{{ $j->kategori }}</x-badge>
                        @endif
                    </div>
                    <div class="text-sm text-zinc-500 flex flex-wrap gap-x-4 gap-y-1">
                        <span class="inline-flex items-center gap-1"><x-icon name="clock" class="w-4 h-4" /> {{ \Carbon\Carbon::parse($j->jam)->format('H:i') }} WIB</span>
                        <span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="w-4 h-4" /> {{ $j->lokasi }}</span>
                    </div>
                </div>
                <x-icon name="chevron-down" class="w-5 h-5 text-zinc-400 transition-transform" data-arrow />
            </button>

            <div class="hidden border-t border-zinc-200 bg-zinc-50/40 px-5 py-4">
                <h4 class="text-xs font-semibold uppercase tracking-wide text-zinc-500 mb-1">Deskripsi</h4>
                <p class="text-sm text-zinc-700 whitespace-pre-line">{{ $j->deskripsi }}</p>

                @if($isManager)
                <div class="flex gap-2 mt-4">
                    <x-button href="{{ route('jadwal.edit', $j) }}" variant="secondary" size="sm" icon="edit" class="flex-1">Edit</x-button>
                    <form action="{{ route('jadwal.destroy', $j) }}" method="POST" class="flex-1"
                          onsubmit="return confirm('Hapus jadwal ini?');">
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

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const filter = this.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-brand-700', 'text-white');
            b.classList.add('bg-white', 'text-zinc-700', 'border', 'border-zinc-200', 'hover:bg-zinc-50');
        });
        this.classList.remove('bg-white', 'text-zinc-700', 'border', 'border-zinc-200', 'hover:bg-zinc-50');
        this.classList.add('bg-brand-700', 'text-white');

        document.querySelectorAll('.jadwal-item').forEach(it => {
            it.style.display = (filter === 'semua' || it.dataset.status === filter) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
