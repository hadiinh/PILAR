@extends('layouts.app')

@section('title', 'Catat Transaksi')

@section('content')
<x-page-header title="Catat Transaksi"
               subtitle="Catat pemasukan atau pengeluaran kas RW." />

<x-flash />

<x-card>
    <form action="{{ route('keuangan.store') }}" method="POST" class="space-y-5 max-w-2xl">
        @csrf

        <x-input name="judul" label="Keterangan" placeholder="Contoh: Iuran bulan Mei dari RT 03" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-select name="tipe" label="Tipe Transaksi" required>
                <option value="masuk" {{ old('tipe') === 'masuk' ? 'selected' : '' }}>Pemasukan</option>
                <option value="keluar" {{ old('tipe') === 'keluar' ? 'selected' : '' }}>Pengeluaran</option>
            </x-select>
            <x-input name="jumlah" type="number" min="0" step="1" label="Jumlah (Rp)" placeholder="50000" required />
        </div>

        <x-input name="tanggal" type="date" label="Tanggal Transaksi" :value="now()->toDateString()" required />

        <x-textarea name="deskripsi" label="Catatan (opsional)" rows="4"
                    placeholder="Tuliskan rincian agar mudah dilacak." />

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('keuangan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Transaksi</x-button>
        </div>
    </form>
</x-card>
@endsection
