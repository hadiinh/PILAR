@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<x-page-header title="Edit Transaksi" subtitle="Perbarui detail transaksi kas." />

<x-flash />

<x-card>
    <form action="{{ route('keuangan.update', $keuangan) }}" method="POST" class="space-y-5 max-w-2xl">
        @csrf @method('PUT')

        <x-input name="judul" label="Keterangan" :value="$keuangan->judul" required />

        <div class="grid sm:grid-cols-2 gap-4">
            <x-select name="tipe" label="Tipe Transaksi" required>
                <option value="masuk" @selected($keuangan->tipe === 'masuk')>Pemasukan</option>
                <option value="keluar" @selected($keuangan->tipe === 'keluar')>Pengeluaran</option>
            </x-select>
            <x-input name="jumlah" rupiah label="Jumlah (Rp)" :value="$keuangan->jumlah" required />
        </div>

        <x-input name="tanggal" type="date" label="Tanggal Transaksi" :value="$keuangan->tanggal" required />

        <x-textarea name="deskripsi" label="Catatan" rows="4" :value="$keuangan->deskripsi" />

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('keuangan.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>
@endsection
