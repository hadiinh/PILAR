@extends('layouts.app')

@section('title', 'Pengajuan Akun')

@section('content')
<x-page-header title="Pengajuan Akun Warga"
               subtitle="Tinjau dan setujui pengajuan akun dari warga." />

<x-flash />

<div class="grid grid-cols-3 gap-3 mb-6">
    <x-stat label="Menunggu" value="{{ $stats['pending'] }}" icon="clock" tone="warning" />
    <x-stat label="Disetujui" value="{{ $stats['disetujui'] }}" icon="check" tone="success" />
    <x-stat label="Ditolak" value="{{ $stats['ditolak'] }}" icon="close" tone="danger" />
</div>

<x-card>
    <form method="GET" class="flex flex-col sm:flex-row gap-2 mb-4">
        <div class="flex gap-1 rounded-lg bg-zinc-100 p-1 text-xs font-semibold">
            @php
                $tabs = [
                    ''          => 'Semua',
                    'pending'   => 'Menunggu',
                    'disetujui' => 'Disetujui',
                    'ditolak'   => 'Ditolak',
                ];
            @endphp
            @foreach($tabs as $v => $label)
                <a href="{{ route('pengajuan.index', array_filter(['status' => $v, 'q' => $q])) }}"
                   class="px-3 h-8 inline-flex items-center rounded-md {{ $status === $v ? 'bg-white shadow-sm text-zinc-900' : 'text-zinc-600' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <div class="flex-1 flex gap-2">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Cari NIK / nama / no HP"
                   class="flex-1 h-9 px-3 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
            <input type="hidden" name="status" value="{{ $status }}">
            <button type="submit" class="px-3 h-9 rounded-lg bg-brand-700 hover:bg-brand-800 text-white text-sm font-semibold">Cari</button>
        </div>
    </form>

    @if($items->isEmpty())
        <x-empty-state icon="clock" title="Belum ada pengajuan" />
    @else
        {{-- Mobile cards --}}
        <ul class="md:hidden divide-y divide-zinc-100">
            @foreach($items as $p)
                @php
                    $tone = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger'][$p->status] ?? 'neutral';
                @endphp
                <li class="py-3 space-y-1">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <p class="font-semibold text-zinc-900 truncate">{{ $p->user?->name ?? '— belum ada nama —' }}</p>
                            <p class="text-xs text-zinc-500">NIK: {{ $p->nik }}</p>
                            <p class="text-xs text-zinc-500">No HP: {{ $p->no_hp }}</p>
                        </div>
                        <x-badge :variant="$tone">{{ $p->statusLabel() }}</x-badge>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs text-zinc-500">{{ $p->created_at->translatedFormat('d M Y H:i') }}</span>
                        <a href="{{ route('pengajuan.show', $p) }}" class="text-xs font-semibold text-brand-700 hover:underline">Detail</a>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto -mx-2">
            <table class="w-full text-sm">
                <thead class="text-left text-zinc-500">
                    <tr class="border-b border-zinc-200">
                        <th class="py-2 px-2 font-semibold">Tanggal</th>
                        <th class="py-2 px-2 font-semibold">Nama</th>
                        <th class="py-2 px-2 font-semibold">NIK</th>
                        <th class="py-2 px-2 font-semibold">No HP</th>
                        <th class="py-2 px-2 font-semibold">Status</th>
                        <th class="py-2 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $p)
                        @php
                            $tone = ['pending'=>'warning','disetujui'=>'success','ditolak'=>'danger'][$p->status] ?? 'neutral';
                        @endphp
                        <tr class="border-b border-zinc-100">
                            <td class="py-3 px-2 text-zinc-500 whitespace-nowrap">{{ $p->created_at->translatedFormat('d M Y H:i') }}</td>
                            <td class="py-3 px-2 font-semibold text-zinc-900">{{ $p->user?->name ?? '—' }}</td>
                            <td class="py-3 px-2 text-zinc-700 font-mono text-xs">{{ $p->nik }}</td>
                            <td class="py-3 px-2 text-zinc-700">{{ $p->no_hp }}</td>
                            <td class="py-3 px-2"><x-badge :variant="$tone">{{ $p->statusLabel() }}</x-badge></td>
                            <td class="py-3 px-2 text-right">
                                <a href="{{ route('pengajuan.show', $p) }}"
                                   class="inline-flex items-center gap-1 px-3 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Detail</a>
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
