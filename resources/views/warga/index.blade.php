@extends('layouts.app')

@section('title', 'Manajemen Warga')

@section('content')
<x-page-header title="Manajemen Warga"
               subtitle="Kelola data kependudukan RW 016.">
    <x-button href="{{ route('warga.create') }}" variant="primary" icon="plus">Tambah Warga</x-button>
</x-page-header>

<x-flash />

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <x-stat label="Total Akun" value="{{ $stats['total'] }}" icon="users" tone="brand" />
    <x-stat label="Akun Aktif" value="{{ $stats['aktif'] }}" icon="check" tone="success" />
    <x-stat label="Akun Nonaktif" value="{{ $stats['nonaktif'] }}" icon="close" tone="danger" />
    <x-stat label="Total Warga" value="{{ $stats['warga'] }}" icon="user" tone="info" />
</div>

<x-card>
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / NIK / KK / no HP"
               class="sm:col-span-2 h-10 px-3 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
        <select name="role" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Semua peran</option>
            <option value="user" @selected($role==='user')>Warga</option>
            <option value="ketua_rw" @selected($role==='ketua_rw')>Ketua RW</option>
            <option value="admin" @selected($role==='admin')>Admin</option>
        </select>
        <select name="aktif" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Status akun</option>
            <option value="1" @selected($aktif==='1')>Aktif</option>
            <option value="0" @selected($aktif==='0')>Nonaktif</option>
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
                                <x-badge :variant="$u->role === 'ketua_rw' ? 'brand' : ($u->role === 'admin' ? 'info' : 'neutral')">
                                    {{ ucwords(str_replace('_',' ', $u->role)) }}
                                </x-badge>
                                @if($u->akun_aktif)
                                    <x-badge variant="success">Aktif</x-badge>
                                @else
                                    <x-badge variant="danger">Nonaktif</x-badge>
                                @endif
                                @if($u->kategori_umur_label)
                                    <x-badge variant="info">{{ $u->kategori_umur_label }}</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 pt-1">
                        <a href="{{ route('warga.edit', $u) }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>
                        <span class="text-zinc-300">·</span>
                        <form action="{{ route('warga.resetPassword', $u) }}" method="POST" class="inline"
                              onsubmit="return confirm('Reset kata sandi {{ $u->name }}?');">
                            @csrf
                            <button class="text-xs font-semibold text-zinc-700 hover:underline">Reset Password</button>
                        </form>
                        @if($u->akun_aktif)
                            <span class="text-zinc-300">·</span>
                            <form action="{{ route('warga.deactivate', $u) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Nonaktifkan akun {{ $u->name }}?');">
                                @csrf
                                <button class="text-xs font-semibold text-red-600 hover:underline">Nonaktifkan</button>
                            </form>
                        @else
                            <span class="text-zinc-300">·</span>
                            <form action="{{ route('warga.activate', $u) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-xs font-semibold text-emerald-700 hover:underline">Aktifkan</button>
                            </form>
                        @endif
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
                        <th class="py-2 px-2 font-semibold">Peran</th>
                        <th class="py-2 px-2 font-semibold">Status</th>
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
                                            <p class="text-xs text-zinc-500">{{ $u->kategori_umur_label }} · {{ $u->umur }} th</p>
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
                                    @if($u->no_rumah) · No.{{ $u->no_rumah }} @endif
                                @endif
                                @if($u->kelurahan_nama)<br><span class="text-zinc-500">{{ $u->kelurahan_nama }}</span>@endif
                            </td>
                            <td class="py-3 px-2">
                                <x-badge :variant="$u->role === 'ketua_rw' ? 'brand' : ($u->role === 'admin' ? 'info' : 'neutral')">
                                    {{ ucwords(str_replace('_',' ', $u->role)) }}
                                </x-badge>
                            </td>
                            <td class="py-3 px-2">
                                @if($u->akun_aktif)
                                    <x-badge variant="success">Aktif</x-badge>
                                @else
                                    <x-badge variant="danger">Nonaktif</x-badge>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-right whitespace-nowrap">
                                <div class="inline-flex gap-1">
                                    <a href="{{ route('warga.edit', $u) }}"
                                       class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Edit</a>
                                    <form action="{{ route('warga.resetPassword', $u) }}" method="POST"
                                          onsubmit="return confirm('Reset kata sandi {{ $u->name }}?');">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Reset</button>
                                    </form>
                                    @if($u->akun_aktif)
                                        <form action="{{ route('warga.deactivate', $u) }}" method="POST"
                                              onsubmit="return confirm('Nonaktifkan akun {{ $u->name }}?');">
                                            @csrf
                                            <button class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-red-200 text-red-700 hover:bg-red-50">Nonaktif</button>
                                        </form>
                                    @else
                                        <form action="{{ route('warga.activate', $u) }}" method="POST">
                                            @csrf
                                            <button class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-emerald-200 text-emerald-700 hover:bg-emerald-50">Aktifkan</button>
                                        </form>
                                    @endif
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
