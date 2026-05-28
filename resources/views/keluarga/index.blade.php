@extends('layouts.app')

@section('title', 'Data Keluarga')

@section('content')
<x-page-header title="Data Keluarga"
               subtitle="Daftar Kartu Keluarga yang terdaftar di RW 016." />

<x-flash />

<div class="grid grid-cols-3 gap-3 mb-6">
    <x-stat label="Total KK" value="{{ $stats['total_kk'] }}" icon="users" tone="brand" />
    <x-stat label="Total Anggota" value="{{ $stats['total_anggota'] }}" icon="user" tone="info" />
    <x-stat label="Kepala Keluarga" value="{{ $stats['kepala'] }}" icon="check" tone="success" />
</div>

<x-card>
    <form method="GET" class="flex gap-2 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari No KK atau nama anggota"
               class="flex-1 h-10 px-3 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
        <button type="submit" class="px-4 h-10 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold">Cari</button>
    </form>

    @if($items->isEmpty())
        <x-empty-state icon="users" title="Belum ada data keluarga" description="Tambahkan warga dengan nomor KK untuk membentuk keluarga." />
    @else
        {{-- Mobile cards --}}
        <ul class="md:hidden divide-y divide-zinc-100">
            @foreach($items as $kk)
                <li class="py-3">
                    <a href="{{ route('keluarga.show', $kk->no_kk) }}" class="flex items-start gap-3">
                        <span class="w-10 h-10 rounded-lg bg-brand-50 text-brand-700 flex items-center justify-center font-semibold shrink-0">
                            {{ $kk->jumlah_anggota }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-zinc-900 truncate">{{ $kk->nama_kepala ?? 'Belum ada kepala keluarga' }}</p>
                            <p class="text-xs text-zinc-500 font-mono">{{ $kk->no_kk }}</p>
                            <p class="text-xs text-zinc-500">
                                @if($kk->rt)RT {{ str_pad($kk->rt,2,'0',STR_PAD_LEFT) }}/RW {{ str_pad($kk->rw ?? '016',3,'0',STR_PAD_LEFT) }}@endif
                                @if($kk->no_rumah) · No.{{ $kk->no_rumah }}@endif
                            </p>
                        </div>
                        <x-icon name="chevron-right" class="w-4 h-4 text-zinc-400 self-center" />
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto -mx-2">
            <table class="w-full text-sm">
                <thead class="text-left text-zinc-500">
                    <tr class="border-b border-zinc-200">
                        <th class="py-2 px-2 font-semibold">No KK</th>
                        <th class="py-2 px-2 font-semibold">Kepala Keluarga</th>
                        <th class="py-2 px-2 font-semibold text-center">Anggota</th>
                        <th class="py-2 px-2 font-semibold">Alamat</th>
                        <th class="py-2 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $kk)
                        <tr class="border-b border-zinc-100">
                            <td class="py-3 px-2 font-mono text-xs text-zinc-700">{{ $kk->no_kk }}</td>
                            <td class="py-3 px-2 font-semibold text-zinc-900">{{ $kk->nama_kepala ?? '— belum ditandai —' }}</td>
                            <td class="py-3 px-2 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-50 text-brand-800 font-bold text-sm">
                                    {{ $kk->jumlah_anggota }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-xs text-zinc-700">
                                @if($kk->rt)RT {{ str_pad($kk->rt,2,'0',STR_PAD_LEFT) }}/RW {{ str_pad($kk->rw ?? '016',3,'0',STR_PAD_LEFT) }}@endif
                                @if($kk->no_rumah) · No.{{ $kk->no_rumah }}@endif
                                @if($kk->kelurahan_nama)<br><span class="text-zinc-500">{{ $kk->kelurahan_nama }}, {{ $kk->kecamatan_nama }}</span>@endif
                            </td>
                            <td class="py-3 px-2 text-right">
                                <a href="{{ route('keluarga.show', $kk->no_kk) }}"
                                   class="inline-flex items-center gap-1 px-3 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $items->links() }}</div>
    @endif
</x-card>
@endsection
