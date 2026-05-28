@extends('layouts.app')

@section('title', 'Statistik Warga')

@section('content')
<x-page-header title="Statistik Kependudukan"
               subtitle="Gambaran umum data warga RW 016." />

<x-flash />

{{-- Kartu statistik utama --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <x-stat label="Total Warga" value="{{ $stats['total_warga'] }}" icon="users" tone="brand" />
    <x-stat label="Total KK" value="{{ $stats['total_kk'] }}" icon="users" tone="info" />
    <x-stat label="Laki-laki" value="{{ $stats['total_laki'] }}" icon="user" tone="info" />
    <x-stat label="Perempuan" value="{{ $stats['total_perempuan'] }}" icon="user" tone="brand" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <x-stat label="Anak" value="{{ $stats['total_anak'] }}" icon="user" tone="success"
            help="Usia 0-12 tahun" />
    <x-stat label="Remaja" value="{{ $stats['total_remaja'] }}" icon="user" tone="info"
            help="Usia 13-17 tahun" />
    <x-stat label="Dewasa" value="{{ $stats['total_dewasa'] }}" icon="user" tone="brand"
            help="Usia 18-59 tahun" />
    <x-stat label="Lansia" value="{{ $stats['total_lansia'] }}" icon="user" tone="warning"
            help="Usia 60 tahun ke atas" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <x-stat label="Akun Aktif" value="{{ $stats['akun_aktif'] }}" icon="check" tone="success" />
    <x-stat label="Akun Nonaktif" value="{{ $stats['akun_nonaktif'] }}" icon="close" tone="danger" />
    <x-stat label="Akun Pending" value="{{ $stats['akun_pending'] }}" icon="clock" tone="warning" />
    <x-stat label="Total Pengguna" value="{{ $stats['akun_aktif'] + $stats['akun_nonaktif'] }}" icon="users" tone="neutral" />
</div>

{{-- Grafik --}}
<div class="grid lg:grid-cols-2 gap-4 mb-4">
    <x-card>
        <h3 class="font-semibold text-zinc-900 mb-1">Kategori Umur</h3>
        <p class="text-xs text-zinc-500 mb-3">Distribusi warga berdasarkan kelompok umur.</p>
        <div class="relative" style="height: 280px;">
            <canvas id="chartKategori"></canvas>
        </div>
    </x-card>

    <x-card>
        <h3 class="font-semibold text-zinc-900 mb-1">Jenis Kelamin</h3>
        <p class="text-xs text-zinc-500 mb-3">Perbandingan laki-laki dan perempuan.</p>
        <div class="relative" style="height: 280px;">
            <canvas id="chartGender"></canvas>
        </div>
    </x-card>
</div>

<div class="grid lg:grid-cols-2 gap-4 mb-4">
    <x-card>
        <h3 class="font-semibold text-zinc-900 mb-1">Status Akun</h3>
        <p class="text-xs text-zinc-500 mb-3">Akun aktif, nonaktif, dan pengajuan menunggu.</p>
        <div class="relative" style="height: 280px;">
            <canvas id="chartAkun"></canvas>
        </div>
    </x-card>

    <x-card>
        <h3 class="font-semibold text-zinc-900 mb-1">Warga per RT</h3>
        <p class="text-xs text-zinc-500 mb-3">Jumlah warga di setiap RT.</p>
        <div class="relative" style="height: 280px;">
            <canvas id="chartRt"></canvas>
        </div>
    </x-card>
</div>

<x-card class="mb-4">
    <h3 class="font-semibold text-zinc-900 mb-1">Pertumbuhan Pengguna</h3>
    <p class="text-xs text-zinc-500 mb-3">Penambahan akun terdaftar 12 bulan terakhir.</p>
    <div class="relative" style="height: 280px;">
        <canvas id="chartGrowth"></canvas>
    </div>
</x-card>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const start = () => {
        if (typeof Chart === 'undefined') return setTimeout(start, 50);

        const data = @json($chart);

        const palette = {
            primary:   '#245644',
            secondary: '#3a8567',
            light:     '#82bda1',
            navy:      '#1f5fa6',
            blue:      '#3b82f6',
            amber:     '#d97706',
            red:       '#dc2626',
            gray:      '#71717a',
            grid:      '#e4e4e7',
            text:      '#52525b',
        };

        const commonOpts = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: palette.text,
                        boxWidth: 12,
                        boxHeight: 12,
                        font: { size: 11, weight: '600' },
                        padding: 12,
                    },
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#18181b',
                    bodyColor: '#3f3f46',
                    borderColor: '#e4e4e7',
                    borderWidth: 1,
                    padding: 10,
                },
            },
        };

        // Kategori (doughnut)
        new Chart(document.getElementById('chartKategori'), {
            type: 'doughnut',
            data: {
                labels: data.kategori.labels,
                datasets: [{
                    data: data.kategori.data,
                    backgroundColor: [palette.secondary, palette.navy, palette.primary, palette.amber],
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: { ...commonOpts, cutout: '60%' },
        });

        // Gender (pie)
        new Chart(document.getElementById('chartGender'), {
            type: 'pie',
            data: {
                labels: data.gender.labels,
                datasets: [{
                    data: data.gender.data,
                    backgroundColor: [palette.navy, palette.secondary],
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: commonOpts,
        });

        // Akun (bar)
        new Chart(document.getElementById('chartAkun'), {
            type: 'bar',
            data: {
                labels: data.akun.labels,
                datasets: [{
                    label: 'Jumlah',
                    data: data.akun.data,
                    backgroundColor: [palette.secondary, palette.red, palette.amber],
                    borderRadius: 6,
                    maxBarThickness: 60,
                }],
            },
            options: {
                ...commonOpts,
                plugins: { ...commonOpts.plugins, legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: palette.text } },
                    y: { beginAtZero: true, grid: { color: palette.grid }, ticks: { color: palette.text, precision: 0 } },
                },
            },
        });

        // Warga per RT (bar horizontal-friendly)
        new Chart(document.getElementById('chartRt'), {
            type: 'bar',
            data: {
                labels: data.rt.labels,
                datasets: [{
                    label: 'Warga',
                    data: data.rt.data,
                    backgroundColor: palette.primary,
                    borderRadius: 6,
                    maxBarThickness: 32,
                }],
            },
            options: {
                ...commonOpts,
                plugins: { ...commonOpts.plugins, legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: palette.text } },
                    y: { beginAtZero: true, grid: { color: palette.grid }, ticks: { color: palette.text, precision: 0 } },
                },
            },
        });

        // Growth (line)
        new Chart(document.getElementById('chartGrowth'), {
            type: 'line',
            data: {
                labels: data.growth.labels,
                datasets: [{
                    label: 'Akun baru',
                    data: data.growth.data,
                    borderColor: palette.navy,
                    backgroundColor: 'rgba(31,95,166,0.10)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: palette.navy,
                    pointBorderWidth: 2,
                }],
            },
            options: {
                ...commonOpts,
                plugins: { ...commonOpts.plugins, legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: palette.text } },
                    y: { beginAtZero: true, grid: { color: palette.grid }, ticks: { color: palette.text, precision: 0 } },
                },
            },
        });
    };
    start();
});
</script>
@endpush
@endsection
