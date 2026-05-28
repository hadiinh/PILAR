@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php $u = auth()->user(); @endphp

<x-page-header title="Halo, {{ explode(' ', $u->name)[0] }}"
               subtitle="Ringkasan aktivitas RW 016 untuk Anda.">
    @if($isManager)
        <x-button href="{{ url('/jadwal/create') }}" variant="primary" icon="plus">Tambah Jadwal</x-button>
        <x-button href="{{ url('/keuangan/create') }}" variant="secondary" icon="plus">Catat Kas</x-button>
    @else
        <x-button href="{{ url('/laporan/create') }}" variant="primary" icon="plus">Buat Laporan</x-button>
    @endif
</x-page-header>

<x-flash />

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <x-stat label="Saldo Kas" value="Rp {{ number_format($stats['saldo_kas']) }}" icon="wallet" tone="brand"
            help="Pemasukan dikurangi pengeluaran" />
    <x-stat label="Jadwal Mendatang" value="{{ $stats['jadwal_mendatang'] }}" icon="calendar" tone="info" />
    <x-stat label="Laporan Baru" value="{{ $stats['laporan_baru'] }}" icon="flag" tone="warning" />
    <x-stat label="Dokumentasi" value="{{ $stats['total_foto'] }}" icon="image" tone="neutral" />
</div>

@if($isManager)
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
    <x-stat label="Pemasukan" value="Rp {{ number_format($stats['total_masuk']) }}" icon="arrow-up" tone="success" />
    <x-stat label="Pengeluaran" value="Rp {{ number_format($stats['total_keluar']) }}" icon="arrow-down" tone="danger" />
    <x-stat label="Warga Terdaftar" value="{{ $stats['total_user'] }}" icon="users" tone="info" />
    <x-stat label="Total Kegiatan" value="{{ $stats['total_kegiatan'] }}" icon="megaphone" tone="neutral" />
</div>

{{-- ===== Grafik Keuangan ===== --}}
<x-card class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
        <div>
            <h2 class="font-semibold text-zinc-900">Grafik Keuangan RW</h2>
            <p class="text-xs text-zinc-500">Perkembangan kas 12 bulan terakhir.</p>
        </div>
        <div class="hidden sm:flex gap-1 rounded-lg bg-zinc-100 p-1 text-xs font-semibold">
            <button type="button" data-chart-view="bar"
                    class="chart-toggle px-3 h-8 rounded-md bg-white shadow-sm text-zinc-900">Bulanan</button>
            <button type="button" data-chart-view="line"
                    class="chart-toggle px-3 h-8 rounded-md text-zinc-600">Saldo Berjalan</button>
        </div>
    </div>

    <div class="relative" style="height: 320px;">
        <canvas id="chartKeuangan"></canvas>
    </div>

    <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-zinc-200 text-center">
        <div>
            <p class="text-[11px] text-zinc-500 font-semibold uppercase tracking-wide">Pemasukan</p>
            <p class="text-sm font-bold text-emerald-700">Rp {{ number_format(array_sum($chart['pemasukan'])) }}</p>
        </div>
        <div>
            <p class="text-[11px] text-zinc-500 font-semibold uppercase tracking-wide">Pengeluaran</p>
            <p class="text-sm font-bold text-red-700">Rp {{ number_format(array_sum($chart['pengeluaran'])) }}</p>
        </div>
        <div>
            <p class="text-[11px] text-zinc-500 font-semibold uppercase tracking-wide">Saldo Akhir</p>
            <p class="text-sm font-bold text-brand-700">Rp {{ number_format(end($chart['saldo']) ?: 0) }}</p>
        </div>
    </div>
</x-card>
@endif

{{-- Two-column body --}}
<div class="grid lg:grid-cols-3 gap-4">

    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Jadwal Terbaru</h2>
            <a href="{{ url('/jadwal') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>

        @forelse($recentJadwal as $j)
            @php $tgl = \Carbon\Carbon::parse($j->tanggal); @endphp
            <div class="flex gap-3 py-3 border-t border-zinc-200 first:border-t-0">
                <div class="w-14 shrink-0 text-center bg-brand-50 text-brand-800 rounded-lg p-2">
                    <p class="text-[10px] font-semibold uppercase">{{ $tgl->translatedFormat('M') }}</p>
                    <p class="text-xl font-bold leading-none">{{ $tgl->format('d') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-zinc-900 truncate">{{ $j->judul }}</p>
                    <p class="text-xs text-zinc-500 truncate">
                        <x-icon name="clock" class="inline w-3.5 h-3.5 -mt-0.5" /> {{ \Carbon\Carbon::parse($j->jam)->format('H:i') }} WIB
                        · <x-icon name="map-pin" class="inline w-3.5 h-3.5 -mt-0.5" /> {{ $j->lokasi }}
                    </p>
                </div>
                @if($j->kategori)
                    <x-badge variant="info" class="self-center hidden sm:inline-flex">{{ $j->kategori }}</x-badge>
                @endif
            </div>
        @empty
            <x-empty-state icon="calendar"
                           title="Belum ada jadwal"
                           description="Jadwal kegiatan RW akan ditampilkan di sini." />
        @endforelse
    </x-card>

    <x-card>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">{{ $isManager ? 'Transaksi Terbaru' : 'Laporan Saya' }}</h2>
            <a href="{{ url($isManager ? '/keuangan' : '/laporan') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>

        @if($isManager)
            @forelse($recentKeuangan as $k)
                <div class="flex items-center gap-3 py-2.5 border-t border-zinc-200 first:border-t-0">
                    <span class="w-8 h-8 rounded-full flex items-center justify-center {{ $k->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        <x-icon :name="$k->tipe === 'masuk' ? 'arrow-up' : 'arrow-down'" class="w-4 h-4" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-zinc-900 truncate">{{ $k->judul }}</p>
                        <p class="text-xs text-zinc-500">{{ \Carbon\Carbon::parse($k->tanggal)->translatedFormat('d M Y') }}</p>
                    </div>
                    <p class="text-sm font-bold {{ $k->tipe === 'masuk' ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $k->tipe === 'masuk' ? '+' : '−' }} Rp {{ number_format($k->jumlah) }}
                    </p>
                </div>
            @empty
                <x-empty-state icon="wallet" title="Belum ada transaksi" />
            @endforelse
        @else
            @forelse($recentLaporan as $l)
                @php
                    $tone = ['baru' => 'warning', 'diproses' => 'info', 'selesai' => 'success'][$l->status] ?? 'neutral';
                @endphp
                <div class="py-2.5 border-t border-zinc-200 first:border-t-0">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-zinc-900 truncate">{{ $l->judul }}</p>
                        <x-badge :variant="$tone">{{ ucfirst($l->status) }}</x-badge>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ \Carbon\Carbon::parse($l->tanggal)->translatedFormat('d M Y') }}</p>
                </div>
            @empty
                <x-empty-state icon="flag"
                               title="Belum ada laporan"
                               description="Sampaikan masalah lingkungan dengan tombol di atas.">
                </x-empty-state>
            @endforelse
        @endif
    </x-card>
</div>

{{-- Quick links / dokumentasi --}}
<div class="mt-6 grid lg:grid-cols-3 gap-4">
    <x-card class="lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-zinc-900">Dokumentasi Terbaru</h2>
            <a href="{{ url('/foto') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">Lihat semua</a>
        </div>
        @if($recentFoto->count())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($recentFoto as $f)
                    <a href="{{ url('/foto/'.$f->id) }}" class="group block">
                        <div class="aspect-[4/3] overflow-hidden rounded-lg bg-zinc-100">
                            <img src="{{ asset('storage/'.$f->gambar) }}"
                                 alt="{{ $f->judul }}"
                                 class="w-full h-full object-cover group-hover:opacity-90">
                        </div>
                        <p class="mt-1.5 text-xs font-medium text-zinc-700 truncate">{{ $f->judul }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <x-empty-state icon="image" title="Belum ada foto" />
        @endif
    </x-card>

    <x-card>
        <h2 class="font-semibold text-zinc-900 mb-3">Akses Cepat</h2>
        <div class="grid grid-cols-2 gap-2">
            @php
                $quick = [
                    ['Beranda', '/beranda', 'home'],
                    ['Jadwal', '/jadwal', 'calendar'],
                    ['Foto', '/foto', 'image'],
                    ['Keuangan', '/keuangan', 'wallet'],
                    ['Laporan', '/laporan', 'flag'],
                    ['Kegiatan', '/kegiatan', 'megaphone'],
                ];
            @endphp
            @foreach($quick as [$label, $href, $ic])
                <a href="{{ url($href) }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-zinc-200 hover:bg-zinc-50 text-zinc-700">
                    <x-icon :name="$ic" class="w-6 h-6 text-brand-700" />
                    <span class="text-xs font-semibold">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </x-card>
</div>

@if($isManager)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const start = () => {
        if (typeof Chart === 'undefined') return setTimeout(start, 50);

        const labels      = @json($chart['labels']);
        const pemasukan   = @json($chart['pemasukan']);
        const pengeluaran = @json($chart['pengeluaran']);
        const saldo       = @json($chart['saldo']);

        const fmtRupiah = (v) => 'Rp ' + Number(v).toLocaleString('id-ID');

        const ctx = document.getElementById('chartKeuangan');
        if (!ctx) return;

        const colors = {
            masuk:  '#3a8567',
            keluar: '#dc2626',
            saldo:  '#1f5fa6',
            grid:   '#e4e4e7',
            text:   '#52525b',
        };

        const baseTooltip = {
            backgroundColor: '#fff',
            titleColor: '#18181b',
            bodyColor: '#3f3f46',
            borderColor: '#e4e4e7',
            borderWidth: 1,
            padding: 10,
            boxPadding: 6,
            callbacks: {
                label: (c) => c.dataset.label + ': ' + fmtRupiah(c.parsed.y),
            },
        };

        const baseScales = {
            x: { grid: { display: false }, ticks: { color: colors.text, font: { size: 11 } } },
            y: {
                beginAtZero: true,
                grid: { color: colors.grid, drawBorder: false },
                ticks: {
                    color: colors.text,
                    font: { size: 11 },
                    callback: (v) => {
                        if (v >= 1_000_000) return (v / 1_000_000) + ' jt';
                        if (v >= 1_000) return (v / 1_000) + ' rb';
                        return v;
                    },
                },
            },
        };

        let chart;

        function render(view) {
            if (chart) chart.destroy();

            if (view === 'line') {
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Saldo Berjalan',
                            data: saldo,
                            borderColor: colors.saldo,
                            backgroundColor: 'rgba(31,95,166,0.10)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: colors.saldo,
                            pointBorderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: baseTooltip,
                        },
                        scales: baseScales,
                    },
                });
            } else {
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: pemasukan,
                                backgroundColor: colors.masuk,
                                borderRadius: 6,
                                maxBarThickness: 28,
                            },
                            {
                                label: 'Pengeluaran',
                                data: pengeluaran,
                                backgroundColor: colors.keluar,
                                borderRadius: 6,
                                maxBarThickness: 28,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: colors.text,
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    font: { size: 12, weight: '600' },
                                    padding: 14,
                                },
                            },
                            tooltip: baseTooltip,
                        },
                        scales: baseScales,
                    },
                });
            }
        }

        render('bar');

        document.querySelectorAll('.chart-toggle').forEach((btn) => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.chart-toggle').forEach((b) => {
                    b.classList.remove('bg-white', 'shadow-sm', 'text-zinc-900');
                    b.classList.add('text-zinc-600');
                });
                this.classList.add('bg-white', 'shadow-sm', 'text-zinc-900');
                this.classList.remove('text-zinc-600');
                render(this.dataset.chartView);
            });
        });
    };
    start();
});
</script>
@endpush
@endif

@endsection
