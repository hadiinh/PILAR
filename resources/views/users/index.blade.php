@extends('layouts.app')

@section('title', 'Daftar Warga')

@section('content')
@php
    $totalAdmin = $users->where('role', 'admin')->count();
    $totalWarga = $users->where('role', 'user')->count();
    $totalKetua = $users->where('role', 'ketua_rw')->count();
@endphp

<x-page-header title="Daftar Warga"
               subtitle="Manajemen akun pengguna PILAR RW 016." />

<x-flash />

<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <x-stat label="Total Akun" value="{{ $users->count() }}" icon="users" tone="brand" />
    <x-stat label="Ketua RW"    value="{{ $totalKetua }}" icon="user" tone="info" />
    <x-stat label="Admin"        value="{{ $totalAdmin }}" icon="settings" tone="warning" />
    <x-stat label="Warga"        value="{{ $totalWarga }}" icon="user" tone="success" />
</div>

<x-card>
    @if($users->isEmpty())
        <x-empty-state icon="users" title="Belum ada warga terdaftar" />
    @else
    {{-- Mobile cards --}}
    <ul class="md:hidden divide-y divide-zinc-100">
        @foreach($users as $u)
            <li class="py-3 flex items-start gap-3">
                <span class="w-10 h-10 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold shrink-0">
                    {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-900 truncate">{{ $u->name }}</p>
                    <p class="text-xs text-zinc-500 truncate">{{ $u->email }}</p>
                    @if($u->no_hp)
                        <p class="text-xs text-zinc-500 truncate">{{ $u->no_hp }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <x-badge :variant="$u->role === 'ketua_rw' ? 'brand' : ($u->role === 'admin' ? 'info' : 'neutral')">
                            {{ ucwords(str_replace('_',' ', $u->role)) }}
                        </x-badge>
                        @if($u->rt)<span class="text-xs text-zinc-500">RT {{ str_pad($u->rt, 2, '0', STR_PAD_LEFT) }}</span>@endif
                        @if($u->notif_wa_aktif && $u->no_hp)
                            <x-badge variant="success">WA aktif</x-badge>
                        @endif
                    </div>
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
                    <th class="py-2 px-2 font-semibold">Kontak</th>
                    <th class="py-2 px-2 font-semibold">Peran</th>
                    <th class="py-2 px-2 font-semibold">Alamat</th>
                    <th class="py-2 px-2 font-semibold">Notif WA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr class="border-b border-zinc-100">
                    <td class="py-3 px-2">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold">
                                {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                            </span>
                            <span class="font-semibold text-zinc-900">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-2 text-zinc-700">
                        <p>{{ $u->email }}</p>
                        @if($u->no_hp)<p class="text-xs text-zinc-500">{{ $u->no_hp }}</p>@endif
                    </td>
                    <td class="py-3 px-2">
                        <x-badge :variant="$u->role === 'ketua_rw' ? 'brand' : ($u->role === 'admin' ? 'info' : 'neutral')">
                            {{ ucwords(str_replace('_',' ', $u->role)) }}
                        </x-badge>
                    </td>
                    <td class="py-3 px-2 text-zinc-700 max-w-xs">
                        @if($u->rt || $u->no_rumah || $u->kelurahan_nama)
                            <p class="text-xs">
                                @if($u->rt || $u->no_rumah)
                                    RT {{ str_pad($u->rt ?? '-', 2, '0', STR_PAD_LEFT) }}/RW {{ str_pad($u->rw ?? '016', 3, '0', STR_PAD_LEFT) }}
                                    @if($u->no_rumah) No. {{ $u->no_rumah }} @endif
                                @endif
                            </p>
                            @if($u->kelurahan_nama)
                                <p class="text-xs text-zinc-500">{{ $u->kelurahan_nama }}, {{ $u->kecamatan_nama }}</p>
                            @endif
                        @else — @endif
                    </td>
                    <td class="py-3 px-2">
                        @if($u->notif_wa_aktif && $u->no_hp)
                            <x-badge variant="success">Aktif</x-badge>
                        @else
                            <x-badge variant="neutral">Nonaktif</x-badge>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</x-card>
@endsection
