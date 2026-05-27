@extends('layouts.app')

@section('title', 'Keuangan RW')

@section('content')
@php $isManager = auth()->check() && in_array(auth()->user()->role, ['ketua_rw', 'admin']); @endphp

<x-page-header title="Keuangan RW"
               subtitle="Laporan pemasukan & pengeluaran kas, terbuka untuk seluruh warga.">
    @if($isManager)
        <x-button href="{{ route('keuangan.create') }}" variant="primary" icon="plus">Catat Transaksi</x-button>
    @endif
</x-page-header>

<x-flash />

<div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-6">
    <x-stat label="Saldo Kas" value="Rp {{ number_format($saldo) }}" icon="wallet" tone="brand" />
    <x-stat label="Total Pemasukan" value="Rp {{ number_format($totalMasuk) }}" icon="arrow-up" tone="success" />
    <x-stat label="Total Pengeluaran" value="Rp {{ number_format($totalKeluar) }}" icon="arrow-down" tone="danger" />
</div>

<x-card>
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-zinc-900">Riwayat Transaksi</h2>
        <div class="hidden md:flex gap-1 rounded-lg bg-zinc-100 p-1">
            <button type="button" data-filter="semua"
                    class="filter-btn px-3 h-8 text-sm font-semibold rounded-md bg-white shadow-sm">Semua</button>
            <button type="button" data-filter="masuk"
                    class="filter-btn px-3 h-8 text-sm font-semibold rounded-md text-zinc-600">Pemasukan</button>
            <button type="button" data-filter="keluar"
                    class="filter-btn px-3 h-8 text-sm font-semibold rounded-md text-zinc-600">Pengeluaran</button>
        </div>
    </div>

    <div class="md:hidden flex gap-2 overflow-x-auto no-scrollbar mb-3">
        <button type="button" data-filter="semua"
                class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold bg-brand-700 text-white">Semua</button>
        <button type="button" data-filter="masuk"
                class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold bg-white text-zinc-700 border border-zinc-200">Pemasukan</button>
        <button type="button" data-filter="keluar"
                class="filter-btn whitespace-nowrap px-4 h-9 rounded-full text-sm font-semibold bg-white text-zinc-700 border border-zinc-200">Pengeluaran</button>
    </div>

    @if($data->isEmpty())
        <x-empty-state icon="wallet" title="Belum ada transaksi" description="Catat transaksi pertama untuk menjaga transparansi." />
    @else
    {{-- Mobile: card list --}}
    <ul class="md:hidden divide-y divide-zinc-100" id="trxListMobile">
        @foreach($data as $t)
            <li class="trx-row py-3 flex items-center gap-3" data-tipe="{{ $t->tipe }}">
                <span class="w-9 h-9 rounded-full flex items-center justify-center {{ $t->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    <x-icon :name="$t->tipe === 'masuk' ? 'arrow-up' : 'arrow-down'" class="w-4 h-4" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-zinc-900 truncate">{{ $t->judul }}</p>
                    <p class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold {{ $t->tipe === 'masuk' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $t->tipe === 'masuk' ? '+' : '−' }} Rp {{ number_format($t->jumlah) }}
                    </p>
                    @if($isManager)
                    <div class="flex gap-1 mt-1 justify-end">
                        <a href="{{ route('keuangan.edit', $t) }}" class="text-xs font-semibold text-brand-700 hover:underline">Edit</a>
                        <form action="{{ route('keuangan.destroy', $t) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>

    {{-- Desktop: table --}}
    <div class="hidden md:block overflow-x-auto -mx-2">
        <table class="w-full text-sm">
            <thead class="text-left text-zinc-500">
                <tr class="border-b border-zinc-200">
                    <th class="py-2 px-2 font-semibold">Tanggal</th>
                    <th class="py-2 px-2 font-semibold">Keterangan</th>
                    <th class="py-2 px-2 font-semibold">Tipe</th>
                    <th class="py-2 px-2 font-semibold text-right">Jumlah</th>
                    @if($isManager)<th class="py-2 px-2"></th>@endif
                </tr>
            </thead>
            <tbody id="trxList">
                @foreach($data as $t)
                    <tr class="trx-row border-b border-zinc-100" data-tipe="{{ $t->tipe }}">
                        <td class="py-3 px-2 text-zinc-700 whitespace-nowrap">{{ \Carbon\Carbon::parse($t->tanggal)->translatedFormat('d M Y') }}</td>
                        <td class="py-3 px-2">
                            <p class="font-semibold text-zinc-900">{{ $t->judul }}</p>
                            @if($t->deskripsi)<p class="text-xs text-zinc-500">{{ $t->deskripsi }}</p>@endif
                        </td>
                        <td class="py-3 px-2">
                            <x-badge :variant="$t->tipe === 'masuk' ? 'success' : 'danger'">{{ ucfirst($t->tipe) }}</x-badge>
                        </td>
                        <td class="py-3 px-2 text-right font-bold {{ $t->tipe === 'masuk' ? 'text-emerald-700' : 'text-red-700' }} whitespace-nowrap">
                            {{ $t->tipe === 'masuk' ? '+' : '−' }} Rp {{ number_format($t->jumlah) }}
                        </td>
                        @if($isManager)
                        <td class="py-3 px-2 whitespace-nowrap">
                            <div class="flex gap-1 justify-end">
                                <a href="{{ route('keuangan.edit', $t) }}"
                                   class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-zinc-200 hover:bg-zinc-50">
                                    <x-icon name="edit" class="w-3.5 h-3.5" /> Edit
                                </a>
                                <form action="{{ route('keuangan.destroy', $t) }}" method="POST"
                                      onsubmit="return confirm('Hapus transaksi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-2 h-8 text-xs font-semibold rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
                                        <x-icon name="trash" class="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</x-card>

@push('scripts')
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const f = this.dataset.filter;
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('bg-brand-700', 'text-white', 'bg-white', 'shadow-sm');
            b.classList.add('text-zinc-600');
        });
        this.classList.add('bg-brand-700', 'text-white');
        this.classList.remove('text-zinc-600');

        document.querySelectorAll('.trx-row').forEach(r => {
            r.style.display = (f === 'semua' || r.dataset.tipe === f) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
