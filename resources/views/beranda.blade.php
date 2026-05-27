@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
@php
    $u = auth()->user();
@endphp

{{-- Hero card --}}
<x-card padding="p-6 md:p-8" class="!bg-brand-700 !border-brand-700 text-white">
    <div class="flex flex-col md:flex-row md:items-center gap-4">
        <div class="flex-1">
            <p class="text-xs font-semibold tracking-wide uppercase text-brand-100">Selamat datang di</p>
            <h1 class="text-2xl md:text-3xl font-bold mt-1">PILAR RW 016</h1>
            <p class="text-brand-100 mt-1 text-sm">Pusat Informasi dan Layanan RW · Kelurahan Melong, Cimahi Selatan</p>
        </div>
        @auth
            @if(in_array($u->role, ['ketua_rw', 'admin']))
                <a href="{{ url('/dashboard') }}"
                   class="inline-flex items-center gap-2 self-start md:self-auto bg-white text-brand-800 font-semibold px-4 h-10 rounded-lg hover:bg-zinc-100">
                    <x-icon name="building" class="w-4 h-4" />
                    Dashboard Pengurus
                </a>
            @endif
        @endauth
    </div>
</x-card>

<x-flash />

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 my-6">
    <x-stat label="Warga Terdaftar" value="{{ $stats['total_user'] ?? 0 }}" icon="users" tone="info" />
    <x-stat label="Agenda Kegiatan" value="{{ $stats['total_jadwal'] ?? 0 }}" icon="calendar" tone="brand" />
    <x-stat label="Laporan Aktif" value="{{ $stats['laporan_aktif'] ?? 0 }}" icon="flag" tone="warning"
            help="Laporan yang sedang ditangani" />
    <x-stat label="Saldo Kas" value="Rp {{ number_format($stats['saldo_kas'] ?? 0) }}" icon="wallet" tone="success" />
</div>

{{-- Layanan --}}
<x-card class="mb-6">
    <h2 class="font-semibold text-zinc-900 mb-1">Layanan Warga</h2>
    <p class="text-sm text-zinc-500 mb-4">Pilih layanan yang ingin Anda gunakan.</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $services = [
                ['Jadwal',  '/jadwal',  'calendar', 'Lihat agenda kegiatan'],
                ['Foto',    '/foto',    'image',    'Dokumentasi kegiatan'],
                ['Keuangan','/keuangan','wallet',   'Transparansi kas RW'],
                ['Laporan', '/laporan', 'flag',     'Sampaikan keluhan'],
            ];
        @endphp
        @foreach($services as [$label, $href, $ic, $desc])
            <a href="{{ url($href) }}"
               class="group block p-4 rounded-xl border border-zinc-200 hover:border-brand-300 hover:bg-brand-50/40 transition-colors">
                <span class="inline-flex w-10 h-10 rounded-lg bg-brand-50 text-brand-700 items-center justify-center group-hover:bg-brand-100">
                    <x-icon :name="$ic" class="w-5 h-5" />
                </span>
                <p class="mt-3 font-semibold text-zinc-900">{{ $label }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">{{ $desc }}</p>
            </a>
        @endforeach
    </div>
</x-card>

{{-- Kegiatan terbaru + Dokumentasi --}}
<div class="grid lg:grid-cols-3 gap-4 mb-6">
    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Pengumuman & Kegiatan Terbaru</h2>
            <a href="{{ url('/kegiatan') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>

        @forelse($recent_kegiatan as $k)
            <div class="flex gap-3 py-3 border-t border-zinc-200 first:border-t-0">
                <span class="w-10 h-10 shrink-0 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center">
                    <x-icon name="megaphone" class="w-5 h-5" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-900 truncate">{{ $k->judul }}</p>
                    <p class="text-sm text-zinc-600 line-clamp-2">{{ $k->deskripsi }}</p>
                    <p class="text-xs text-zinc-500 mt-1">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}</p>
                </div>
            </div>
        @empty
            <x-empty-state icon="megaphone" title="Belum ada kegiatan" />
        @endforelse
    </x-card>

    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Dokumentasi</h2>
            <a href="{{ url('/foto') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>
        @if(count($recent_fotos) > 0)
            <div class="grid grid-cols-2 gap-2">
                @foreach($recent_fotos->take(4) as $f)
                    <a href="{{ url('/foto/'.$f->id) }}" class="block">
                        <div class="aspect-square overflow-hidden rounded-lg bg-zinc-100">
                            <img src="{{ asset('storage/'.$f->gambar) }}" alt="{{ $f->judul }}" class="w-full h-full object-cover">
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state icon="image" title="Belum ada foto" />
        @endif
    </x-card>
</div>

{{-- Pengurus --}}
<x-card>
    <h2 class="font-semibold text-zinc-900 mb-1">Pengurus RW 016</h2>
    <p class="text-sm text-zinc-500 mb-4">Kelurahan Melong, Cimahi Selatan</p>

    @php
        $pengurus = [
            ['name' => 'H. Bambang Sulistyo', 'role' => 'Ketua RW'],
            ['name' => 'Ibu Ratna Dewi', 'role' => 'Sekretaris'],
        ];
    @endphp
    <div class="grid sm:grid-cols-2 gap-3">
        @foreach($pengurus as $p)
            <div class="flex items-center gap-3 p-3 rounded-lg border border-zinc-200">
                <span class="w-11 h-11 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold">
                    {{ strtoupper(mb_substr($p['name'], 0, 1)) }}
                </span>
                <div>
                    <p class="font-semibold text-zinc-900">{{ $p['name'] }}</p>
                    <p class="text-sm text-zinc-500">{{ $p['role'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</x-card>

@endsection
