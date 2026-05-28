@extends('layouts.app')

@section('title', 'Detail Pengajuan')

@section('content')
@php
    $tone = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger'][$pengajuan->status] ?? 'neutral';
@endphp

<x-page-header title="Detail Pengajuan Akun"
               subtitle="Tinjau data warga sebelum menyetujui atau menolak.">
    <x-button href="{{ route('pengajuan.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<div class="grid lg:grid-cols-3 gap-4">
    <x-card class="lg:col-span-2">
        <div class="flex items-start justify-between gap-3 pb-4 mb-4 border-b border-zinc-200">
            <div>
                <h3 class="font-semibold text-zinc-900 text-lg">{{ $pengajuan->user?->name ?? 'Warga (belum dikenali)' }}</h3>
                <p class="text-xs text-zinc-500">Diajukan {{ $pengajuan->created_at->translatedFormat('d M Y H:i') }} WIB</p>
            </div>
            <x-badge :variant="$tone">{{ $pengajuan->statusLabel() }}</x-badge>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
            <div>
                <dt class="text-zinc-500">NIK</dt>
                <dd class="text-zinc-900 font-mono">{{ $pengajuan->nik }}</dd>
            </div>
            <div>
                <dt class="text-zinc-500">Nomor HP / WA</dt>
                <dd class="text-zinc-900">{{ $pengajuan->no_hp }}</dd>
            </div>
            @if($pengajuan->user)
                <div>
                    <dt class="text-zinc-500">No. KK</dt>
                    <dd class="text-zinc-900 font-mono">{{ $pengajuan->user->no_kk ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Status Warga</dt>
                    <dd class="text-zinc-900 capitalize">{{ $pengajuan->user->status_warga ?? '—' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-zinc-500">Alamat</dt>
                    <dd class="text-zinc-900">{{ $pengajuan->user->alamat_lengkap ?: '—' }}</dd>
                </div>
            @else
                <div class="sm:col-span-2">
                    <dt class="text-zinc-500">Catatan</dt>
                    <dd class="text-red-600">NIK belum terdaftar di tabel warga.</dd>
                </div>
            @endif

            @if($pengajuan->status !== 'pending')
                <div>
                    <dt class="text-zinc-500">Diproses oleh</dt>
                    <dd class="text-zinc-900">{{ $pengajuan->processor?->name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Diproses pada</dt>
                    <dd class="text-zinc-900">{{ $pengajuan->processed_at?->translatedFormat('d M Y H:i') }}</dd>
                </div>
            @endif

            @if($pengajuan->status === 'ditolak' && $pengajuan->alasan_tolak)
                <div class="sm:col-span-2">
                    <dt class="text-zinc-500">Alasan Penolakan</dt>
                    <dd class="text-zinc-900 whitespace-pre-line">{{ $pengajuan->alasan_tolak }}</dd>
                </div>
            @endif
        </dl>
    </x-card>

    <x-card>
        @if($pengajuan->status === 'pending' && $pengajuan->user)
            <h4 class="font-semibold text-zinc-900 mb-3">Tindakan</h4>

            <form action="{{ route('pengajuan.approve', $pengajuan) }}" method="POST"
                  onsubmit="return confirm('Setujui pengajuan ini? Kata sandi baru akan dikirim via WhatsApp.');"
                  class="mb-3">
                @csrf
                <x-button type="submit" variant="primary" icon="check" block>Setujui & Aktifkan Akun</x-button>
            </form>

            <details class="rounded-lg border border-zinc-200">
                <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-red-700">Tolak Pengajuan</summary>
                <form action="{{ route('pengajuan.reject', $pengajuan) }}" method="POST" class="p-3 space-y-3 border-t border-zinc-200">
                    @csrf
                    <x-textarea name="alasan_tolak" label="Alasan Penolakan" rows="3"
                                placeholder="Contoh: Data NIK tidak sesuai dengan data warga RW."
                                required />
                    <x-button type="submit" variant="danger" icon="close" block>Konfirmasi Tolak</x-button>
                </form>
            </details>
        @elseif($pengajuan->status === 'pending' && !$pengajuan->user)
            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-800">
                <p class="font-semibold mb-1">NIK tidak ditemukan</p>
                <p>Pengajuan ini hanya dapat ditolak karena data warga belum terdaftar.</p>
            </div>
            <form action="{{ route('pengajuan.reject', $pengajuan) }}" method="POST" class="mt-3 space-y-3">
                @csrf
                <x-textarea name="alasan_tolak" label="Alasan Penolakan" rows="3"
                            value="Data NIK tidak sesuai dengan data warga RW."
                            required />
                <x-button type="submit" variant="danger" icon="close" block>Tolak Pengajuan</x-button>
            </form>
        @else
            <p class="text-sm text-zinc-500">Pengajuan ini sudah diproses dan tidak dapat diubah.</p>
        @endif
    </x-card>
</div>
@endsection
