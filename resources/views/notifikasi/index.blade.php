@extends('layouts.app')

@section('title', 'Riwayat Notifikasi')

@section('content')
<x-page-header title="Riwayat Notifikasi WhatsApp"
               subtitle="Audit pengiriman pesan via Fonnte." />

<x-flash />

<div class="grid grid-cols-3 gap-2 sm:gap-3 mb-6">
    <x-stat label="Total Pengiriman" value="{{ $stats['total'] }}" icon="message" tone="brand" />
    <x-stat label="Terkirim" value="{{ $stats['terkirim'] }}" icon="check" tone="success" />
    <x-stat label="Gagal" value="{{ $stats['gagal'] }}" icon="close" tone="danger" />
</div>

<x-card>
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-2 mb-4">
        <input type="text" name="q" value="{{ $q }}" placeholder="Cari nomor / nama / pesan"
               class="sm:col-span-2 h-10 px-3 rounded-lg border border-zinc-300 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500" />
        <select name="status" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Semua status</option>
            <option value="terkirim" @selected($status==='terkirim')>Terkirim</option>
            <option value="gagal" @selected($status==='gagal')>Gagal</option>
        </select>
        <select name="jenis" class="h-10 px-3 rounded-lg border border-zinc-300 text-sm">
            <option value="">Semua jenis</option>
            @foreach($jenisList as $j)
                <option value="{{ $j }}" @selected($jenis===$j)>{{ ucwords(str_replace('_',' ', $j)) }}</option>
            @endforeach
        </select>
        <div class="sm:col-span-4">
            <x-button type="submit" variant="primary" icon="search" class="w-full sm:w-auto">Cari</x-button>
        </div>
    </form>

    @if($items->isEmpty())
        <x-empty-state icon="message" title="Belum ada riwayat notifikasi" />
    @else
        {{-- Mobile cards --}}
        <ul class="md:hidden divide-y divide-zinc-100">
            @foreach($items as $log)
                <li class="py-3 space-y-1.5">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-zinc-900 truncate">{{ $log->user?->name ?? '—' }}</p>
                            <p class="text-xs text-zinc-500 font-mono truncate">{{ $log->nomor_tujuan }}</p>
                        </div>
                        @if($log->status === 'terkirim')
                            <x-badge variant="success">Terkirim</x-badge>
                        @else
                            <x-badge variant="danger">Gagal</x-badge>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <x-badge variant="neutral">{{ $log->jenisLabel() }}</x-badge>
                        <span class="text-[11px] text-zinc-500">{{ $log->created_at->translatedFormat('d M Y H:i') }}</span>
                        @if($log->retry_count > 0)
                            <span class="text-[11px] text-zinc-500">· Retry {{ $log->retry_count }}x</span>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-600 line-clamp-2 whitespace-pre-line">{{ $log->pesan }}</p>
                    @if($log->error)
                        <p class="text-[11px] text-red-600"><span class="font-semibold">Error:</span> {{ \Illuminate\Support\Str::limit($log->error, 100) }}</p>
                    @endif
                    @if($log->status === 'gagal')
                        <form action="{{ route('notifikasi.retry', $log) }}" method="POST" class="pt-1">
                            @csrf
                            <button class="inline-flex items-center gap-1 px-3 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Kirim Ulang</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto -mx-2">
            <table class="w-full text-sm">
                <thead class="text-left text-zinc-500">
                    <tr class="border-b border-zinc-200">
                        <th class="py-2 px-2 font-semibold">Waktu</th>
                        <th class="py-2 px-2 font-semibold">Penerima</th>
                        <th class="py-2 px-2 font-semibold">Jenis</th>
                        <th class="py-2 px-2 font-semibold">Pesan</th>
                        <th class="py-2 px-2 font-semibold">Status</th>
                        <th class="py-2 px-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $log)
                        <tr class="border-b border-zinc-100 align-top">
                            <td class="py-3 px-2 text-xs text-zinc-500 whitespace-nowrap">
                                {{ $log->created_at->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="py-3 px-2">
                                <p class="text-sm font-semibold text-zinc-900">{{ $log->user?->name ?? '—' }}</p>
                                <p class="text-xs text-zinc-500 font-mono">{{ $log->nomor_tujuan }}</p>
                            </td>
                            <td class="py-3 px-2 text-xs">
                                <x-badge variant="neutral">{{ $log->jenisLabel() }}</x-badge>
                            </td>
                            <td class="py-3 px-2 text-xs text-zinc-600 max-w-md">
                                <div class="line-clamp-3 whitespace-pre-line">{{ $log->pesan }}</div>
                                @if($log->error)
                                    <p class="text-red-600 mt-1 text-[11px]"><span class="font-semibold">Error:</span> {{ \Illuminate\Support\Str::limit($log->error, 120) }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-2">
                                @if($log->status === 'terkirim')
                                    <x-badge variant="success">Terkirim</x-badge>
                                @else
                                    <x-badge variant="danger">Gagal</x-badge>
                                @endif
                                @if($log->retry_count > 0)
                                    <p class="text-[11px] text-zinc-500 mt-1">Retry: {{ $log->retry_count }}x</p>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-right">
                                @if($log->status === 'gagal')
                                    <form action="{{ route('notifikasi.retry', $log) }}" method="POST">
                                        @csrf
                                        <button class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">Kirim Ulang</button>
                                    </form>
                                @endif
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
