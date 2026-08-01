@extends('layouts.app')

@section('title', 'Data Warga')

@section('content')
<x-page-header title="Manajemen Data Warga"
               subtitle="Kelola data kependudukan warga RW 016.">
    <x-button href="{{ route('data-warga.create') }}" variant="primary" icon="plus">Tambah Warga</x-button>
</x-page-header>

<x-flash />

<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <x-stat label="Total Warga" value="{{ $stats['total'] }}" icon="users" tone="brand" />
    <x-stat label="Warga Tetap" value="{{ $stats['tetap'] }}" icon="home" tone="success" />
    <x-stat label="Kontrak" value="{{ $stats['kontrak'] }}" icon="home" tone="warning" />
    <x-stat label="Laki-laki" value="{{ $stats['laki'] }}" icon="user" tone="info" />
    <x-stat label="Perempuan" value="{{ $stats['perempuan'] }}" icon="user" tone="info" />
</div>

<x-card>
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / NIK / KK / no HP"
               class="sm:col-span-2 h-10 px-3 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
        <select name="status_rumah" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Semua status rumah</option>
            <option value="tetap" @selected($statusRumah==='tetap')>Tetap</option>
            <option value="kontrak" @selected($statusRumah==='kontrak')>Kontrak</option>
        </select>
        <select name="jenis_kelamin" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Semua gender</option>
            <option value="L" @selected($jenisKelamin==='L')>Laki-laki</option>
            <option value="P" @selected($jenisKelamin==='P')>Perempuan</option>
        </select>
        <div class="sm:col-span-4">
            <x-button type="submit" variant="primary" icon="search" class="w-full sm:w-auto">Cari</x-button>
        </div>
    </form>

    @if($items->isEmpty())
        <x-empty-state icon="users" title="Tidak ada data warga sesuai filter" />
    @else
        {{-- Mobile cards --}}
        <ul class="md:hidden divide-y divide-zinc-100">
            @foreach($items as $u)
                <li class="py-3 space-y-1">
                    <div class="flex items-start gap-3">
                        <span class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold shrink-0">
                            {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-zinc-900 truncate">{{ $u->name }}</p>
                            <p class="text-xs text-zinc-500 truncate">NIK: {{ $u->nik ?? '—' }}</p>
                            <p class="text-xs text-zinc-500 truncate">KK: {{ $u->no_kk ?? '—' }}</p>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if($u->status_rumah)
                                    <x-badge :variant="$u->status_rumah === 'tetap' ? 'success' : 'warning'">
                                        {{ ucfirst($u->status_rumah) }}
                                    </x-badge>
                                @endif
                                @if($u->jenis_kelamin)
                                    <x-badge variant="neutral">{{ $u->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</x-badge>
                                @endif
                                @if($u->kategori_umur_label)
                                    <x-badge variant="info">{{ $u->kategori_umur_label }}</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 pt-1">
                        <a href="{{ route('data-warga.edit', $u) }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>
                        <span class="text-zinc-300">|</span>
                        <form action="{{ route('data-warga.destroy', $u) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus data {{ $u->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto -mx-2">
            <table class="w-full text-sm">
                <thead class="text-left text-zinc-500">
                    <tr class="border-b border-zinc-200">
                        <th class="py-2 px-2 font-semibold">Nama</th>
                        <th class="py-2 px-2 font-semibold">NIK / KK</th>
                        <th class="py-2 px-2 font-semibold">Kontak</th>
                        <th class="py-2 px-2 font-semibold">Alamat</th>
                        <th class="py-2 px-2 font-semibold">Status Rumah</th>
                        <th class="py-2 px-2 font-semibold">JK</th>
                        <th class="py-2 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $u)
                        <tr class="border-b border-zinc-100">
                            <td class="py-3 px-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold">
                                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <p class="font-semibold text-zinc-900">{{ $u->name }}</p>
                                        @if($u->kategori_umur_label)
                                            <p class="text-xs text-zinc-500">{{ $u->kategori_umur_label }} - {{ $u->umur }} th</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-2 text-zinc-700 font-mono text-xs">
                                {{ $u->nik ?? '—' }}<br>
                                <span class="text-zinc-400">KK: {{ $u->no_kk ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-2 text-zinc-700">
                                {{ $u->no_hp ?? '—' }}
                                @if($u->email)<br><span class="text-xs text-zinc-500">{{ $u->email }}</span>@endif
                            </td>
                            <td class="py-3 px-2 text-xs text-zinc-700">
                                @if($u->rt || $u->no_rumah)
                                    RT {{ str_pad($u->rt ?? '-', 2, '0', STR_PAD_LEFT) }}/RW {{ str_pad($u->rw ?? '016', 3, '0', STR_PAD_LEFT) }}
                                    @if($u->no_rumah) - No.{{ $u->no_rumah }} @endif
                                @endif
                                @if($u->kelurahan_nama)<br><span class="text-zinc-500">{{ $u->kelurahan_nama }}</span>@endif
                            </td>
                            <td class="py-3 px-2">
                                @if($u->status_rumah)
                                    <x-badge :variant="$u->status_rumah === 'tetap' ? 'success' : 'warning'">
                                        {{ ucfirst($u->status_rumah) }}
                                    </x-badge>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                @if($u->jenis_kelamin)
                                    {{ $u->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-right whitespace-nowrap">
                                <div class="inline-flex gap-1">
                                    <a href="{{ route('data-warga.edit', $u) }}"
                                       class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Edit</a>
                                    <form action="{{ route('data-warga.destroy', $u) }}" method="POST"
                                          onsubmit="return confirm('Hapus data {{ $u->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    @endif
</x-card>
@endsection
