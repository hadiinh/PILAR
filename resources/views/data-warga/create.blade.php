@extends('layouts.app')

@section('title', 'Tambah Data Warga')

@section('content')
<x-page-header title="Tambah Data Warga" subtitle="Tambahkan data kependudukan warga baru.">
    <x-button href="{{ route('data-warga.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<x-card class="max-w-4xl">
    <form action="{{ route('data-warga.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('data-warga._form', ['user' => null])

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('data-warga.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Data</x-button>
        </div>
    </form>
</x-card>
@endsection
