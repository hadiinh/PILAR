@extends('layouts.app')

@section('title', 'Edit Warga')

@section('content')
<x-page-header title="Edit Data Warga" subtitle="{{ $user->name }}">
    <x-button href="{{ route('warga.index') }}" variant="secondary" icon="arrow-left">Kembali</x-button>
</x-page-header>

<x-flash />

<x-card class="max-w-4xl">
    <form action="{{ route('warga.update', $user) }}" method="POST" class="space-y-6">
        @csrf @method('PUT')
        @include('warga._form', ['user' => $user])

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('warga.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>
@endsection
