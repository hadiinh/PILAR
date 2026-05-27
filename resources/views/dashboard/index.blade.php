@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $u = auth()->user(); @endphp

<x-page-header title="Halo, {{ explode(' ', $u->name)[0] }}"
               subtitle="Ringkasan aktivitas RW 016 untuk Anda.">
    @if($isManager)
        <x-button href="{{ url('/jadwal/create') }}" variant="primary" icon="plus">Tambah Jadwal</x-button>
        <x-button href="{{ url('/keuangan/create') }}" variant="secondary" icon="plus">Catat Kas</x-button>
    @else
        <x-button href="{{ url('/laporan/create') }}" variant="primary" icon="plus">Buat Laporan</x-button>
    @endif
</x-page-header>

<x-flash />

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <x-stat label="Saldo Kas" value="Rp {{ number_format($stats['saldo_kas']) }}" icon="wallet" tone="brand"
            help="Pemasukan dikurangi pengeluaran" />
    <x-stat label="Jadwal Mendatang" value="{{ $stats['jadwal_mendatang'] }}" icon="calendar" tone="info" />
    <x-stat label="Laporan Baru" value="{{ $stats['laporan_baru'] }}" icon="flag" tone="warning" />
    <x-stat label="Dokumentasi" value="{{ $stats['total_foto'] }}" icon="image" tone="neutral" />
</div>

@if($isManager)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
    <x-stat label="Pemasukan" value="Rp {{ number_format($stats['total_masuk']) }}" icon="arrow-up" tone="success" />
    <x-stat label="Pengeluaran" value="Rp {{ number_format($stats['total_keluar']) }}" icon="arrow-down" tone="danger" />
    <x-stat label="Warga Terdaftar" value="{{ $stats['total_user'] }}" icon="users" tone="info" />
    <x-stat label="Total Kegiatan" value="{{ $stats['total_kegiatan'] }}" icon="megaphone" tone="neutral" />
</div>
@endif

{{-- Two-column body --}}
<div class="grid lg:grid-cols-3 gap-4">

    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Jadwal Terbaru</h2>
            <a href="{{ url('/jadwal') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>

        @forelse($recentJadwal as $j)
            @php $tgl = \Carbon\Carbon::parse($j->tanggal); @endphp
            <div class="flex gap-3 py-3 border-t border-zinc-200 first:border-t-0">
                <div class="w-14 shrink-0 text-center bg-brand-50 text-brand-800 rounded-lg p-2">
                    <p class="text-[10px] font-semibold uppercase">{{ $tgl->translatedFormat('M') }}</p>
                    <p class="text-xl font-bold leading-none">{{ $tgl->format('d') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-900 truncate">{{ $j->judul }}</p>
                    <p class="text-xs text-zinc-500 truncate">
                        <x-icon name="clock" class="inline w-3.5 h-3.5 -mt-0.5" /> {{ \Carbon\Carbon::parse($j->jam)->format('H:i') }} WIB
                        · <x-icon name="map-pin" class="inline w-3.5 h-3.5 -mt-0.5" /> {{ $j->lokasi }}
                    </p>
                </div>
                @if($j->kategori)
                    <x-badge variant="info" class="self-center hidden sm:inline-flex">{{ $j->kategori }}</x-badge>
                @endif
            </div>
        @empty
            <x-empty-state icon="calendar"
                           title="Belum ada jadwal"
                           description="Jadwal kegiatan RW akan ditampilkan di sini." />
        @endforelse
    </x-card>

    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">{{ $isManager ? 'Transaksi Terbaru' : 'Laporan Saya' }}</h2>
            <a href="{{ url($isManager ? '/keuangan' : '/laporan') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>

        @if($isManager)
            @forelse($recentKeuangan as $k)
                <div class="flex items-center gap-3 py-2.5 border-t border-zinc-200 first:border-t-0">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center {{ $k->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        <x-icon :name="$k->tipe === 'masuk' ? 'arrow-up' : 'arrow-down'" class="w-4 h-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-zinc-900 truncate">{{ $k->judul }}</p>
                        <p class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold {{ $k->tipe === 'masuk' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $k->tipe === 'masuk' ? '+' : '−' }} Rp {{ number_format($k->jumlah) }}
                    </p>
                </div>
            @empty
                <x-empty-state icon="wallet" title="Belum ada transaksi" />
            @endforelse
        @else
            @forelse($recentLaporan as $l)
                @php
                    $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$l->status] ?? 'neutral';
                @endphp
                <div class="py-2.5 border-t border-zinc-200 first:border-t-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-zinc-900 truncate">{{ $l->judul }}</p>
                        <x-badge :variant="$tone">{{ ucfirst($l->status) }}</x-badge>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}</p>
                </div>
            @empty
                <x-empty-state icon="flag"
                               title="Belum ada laporan"
                               description="Sampaikan masalah lingkungan dengan tombol di atas.">
                </x-empty-state>
            @endforelse
        @endif
    </x-card>
</div>

{{-- Quick links / dokumentasi --}}
<div class="mt-6 grid lg:grid-cols-3 gap-4">
    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Dokumentasi Terbaru</h2>
            <a href="{{ url('/foto') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>
        @if($recentFoto->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($recentFoto as $f)
                    <a href="{{ url('/foto/'.$f->id) }}" class="group block">
                        <div class="aspect-[4/3] overflow-hidden rounded-lg bg-zinc-100">
                            <img src="{{ asset('storage/'.$f->gambar) }}"
                                 alt="{{ $f->judul }}"
                                 class="w-full h-full object-cover group-hover:opacity-90">
                        </div>
                        <p class="mt-1.5 text-xs font-medium text-zinc-700 truncate">{{ $f->judul }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state icon="image" title="Belum ada foto" />
        @endif
    </x-card>

    <x-card>
        <h2 class="font-semibold text-zinc-900 mb-3">Akses Cepat</h2>
        <div class="grid grid-cols-2 gap-2">
            @php
                $quick = [
                    ['Beranda', '/beranda', 'home'],
                    ['Jadwal', '/jadwal', 'calendar'],
                    ['Foto', '/foto', 'image'],
                    ['Keuangan', '/keuangan', 'wallet'],
                    ['Laporan', '/laporan', 'flag'],
                    ['Kegiatan', '/kegiatan', 'megaphone'],
                ];
            @endphp
            @foreach($quick as [$label, $href, $ic])
                <a href="{{ url($href) }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700">
                    <x-icon :name="$ic" class="w-6 h-6 text-brand-700" />
                    <span class="text-xs font-semibold">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </x-card>
</div>

@endsection
