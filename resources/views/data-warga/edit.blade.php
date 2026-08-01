@extends('layouts.app')

@section('title', 'Edit Data Warga')

@section('content')
<x-page-header title="Edit Data Warga" :subtitle="'Edit data ' . $user->name">
    <x-button href="{{ route('data-warga.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<x-card class="max-w-4xl">
    <form action="{{ route('data-warga.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        @include('data-warga._form', ['user' => $user])

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('data-warga.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>
@endsection
