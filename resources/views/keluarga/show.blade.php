@extends('layouts.app')

@section('title', 'Detail Keluarga')

@section('content')
<x-page-header title="Detail Keluarga"
               subtitle="Daftar anggota keluarga dengan No. KK {{ $noKk }}.">
    <x-button href="{{ route('keluarga.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<div class="grid lg:grid-cols-3 gap-4">
    {{-- Info KK --}}
    <x-card>
        <h3 class="font-semibold text-zinc-900 mb-3">Informasi Keluarga</h3>
        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-zinc-500">Nomor KK</dt>
                <dd class="font-mono text-zinc-900">{{ $noKk }}</dd>
            </div>
            <div>
                <dt class="text-zinc-500">Kepala Keluarga</dt>
                <dd class="text-zinc-900 font-semibold">{{ $kepala->name }}</dd>
            </div>
            <div>
                <dt class="text-zinc-500">Jumlah Anggota</dt>
                <dd class="text-zinc-900">{{ $anggota->count() }} orang</dd>
            </div>
            <div>
                <dt class="text-zinc-500">Alamat</dt>
                <dd class="text-zinc-900">{{ $kepala->alamat_lengkap ?: '—' }}</dd>
            </div>
        </dl>

        {{-- Komposisi --}}
        @php
            $laki = $anggota->where('jenis_kelamin', 'L')->count();
            $perempuan = $anggota->where('jenis_kelamin', 'P')->count();
            $anak = $anggota->where('kategori_umur', 'anak')->count();
            $remaja = $anggota->where('kategori_umur', 'remaja')->count();
            $dewasa = $anggota->where('kategori_umur', 'dewasa')->count();
            $lansia = $anggota->where('kategori_umur', 'lansia')->count();
        @endphp
        <div class="mt-5 pt-4 border-t border-zinc-200">
            <p class="text-xs font-semibold text-zinc-500 uppercase tracking-wide mb-2">Komposisi</p>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Laki-laki</p>
                    <p class="font-bold text-zinc-900">{{ $laki }}</p>
                </div>
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Perempuan</p>
                    <p class="font-bold text-zinc-900">{{ $perempuan }}</p>
                </div>
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Anak</p>
                    <p class="font-bold text-zinc-900">{{ $anak }}</p>
                </div>
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Remaja</p>
                    <p class="font-bold text-zinc-900">{{ $remaja }}</p>
                </div>
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Dewasa</p>
                    <p class="font-bold text-zinc-900">{{ $dewasa }}</p>
                </div>
                <div class="p-2 rounded-lg bg-zinc-50 border border-zinc-200">
                    <p class="text-zinc-500">Lansia</p>
                    <p class="font-bold text-zinc-900">{{ $lansia }}</p>
                </div>
            </div>
        </div>
    </x-card>

    {{-- Daftar anggota --}}
    <x-card class="lg:col-span-2">
        <h3 class="font-semibold text-zinc-900 mb-3">Anggota Keluarga</h3>

        <ul class="divide-y divide-zinc-100">
            @foreach($anggota as $a)
                <li class="py-3 flex items-start gap-3">
                    <span class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold shrink-0">
                        {{ strtoupper(mb_substr($a->name, 0, 1)) }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-zinc-900">{{ $a->name }}</p>
                            @if($a->is_kepala_keluarga)
                                <x-badge variant="brand">Kepala Keluarga</x-badge>
                            @elseif($a->status_keluarga)
                                <x-badge variant="neutral">{{ ucwords(str_replace('_', ' ', $a->status_keluarga)) }}</x-badge>
                            @endif
                            @if($a->kategori_umur_label)
                                <x-badge variant="info">{{ $a->kategori_umur_label }}</x-badge>
                            @endif
                            @if(!$a->akun_aktif)
                                <x-badge variant="danger">Akun Nonaktif</x-badge>
                            @endif
                        </div>
                        <p class="text-xs text-zinc-500 mt-0.5 font-mono">NIK: {{ $a->nik ?? '—' }}</p>
                        <p class="text-xs text-zinc-500">
                            {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '—') }}
                            @if($a->tanggal_lahir) · {{ $a->tanggal_lahir->translatedFormat('d M Y') }} ({{ $a->umur }} th) @endif
                            @if($a->pekerjaan) · {{ $a->pekerjaan }} @endif
                        </p>
                    </div>
                    <a href="{{ route('warga.edit', $a) }}"
                       class="text-xs font-semibold text-brand-700 hover:underline self-center whitespace-nowrap">Edit</a>
                </li>
            @endforeach
        </ul>
    </x-card>
</div>
@endsection
