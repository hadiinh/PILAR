@extends('layouts.app')

@section('title', 'Tambah Warga')

@section('content')
<x-page-header title="Tambah Warga Baru" subtitle="Daftarkan data warga ke sistem PILAR.">
    <x-button href="{{ route('warga.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<x-card class="max-w-4xl">
    <form action="{{ route('warga.store') }}" method="POST" class="space-y-6">
        @csrf
        @include('warga._form', ['user' => null])

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('warga.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Warga</x-button>
        </div>
    </form>
</x-card>
@endsection
