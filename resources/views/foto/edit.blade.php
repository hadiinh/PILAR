@extends('layouts.app')

@section('title', 'Edit Foto')

@section('content')
<x-page-header title="Edit Foto" subtitle="Perbarui informasi foto." />

<x-flash />

<x-card>
    <form action="{{ route('foto.update', $foto) }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
        @csrf @method('PUT')

        <div>
            <p class="text-xs font-semibold uppercase text-zinc-500 mb-2">Foto saat ini</p>
            <img src="{{ asset('storage/'.$foto->gambar) }}" alt="" class="max-h-60 rounded-lg border border-zinc-200">
        </div>

        <x-input name="judul" label="Judul Foto" :value="$foto->judul" required />

        <x-select name="kategori" label="Kategori" required>
            <option value="">— Pilih kategori —</option>
            @foreach(['Sosial', 'Kebersihan', 'Rapat', 'Olahraga', 'Lainnya'] as $k)
                <option value="{{ $k }}" @selected($foto->kategori === $k)>{{ $k }}</option>
            @endforeach
        </x-select>

        <x-textarea name="deskripsi" label="Deskripsi" rows="4" :value="$foto->deskripsi" />

        <div>
            <label class="block text-sm font-semibold text-zinc-800 mb-1.5">Ganti Foto (opsional)</label>
            <input type="file" name="gambar" id="inputGambar" accept="image/*"
                   class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-zinc-200">
            <div id="previewWrap" class="hidden mt-3">
                <img id="previewImg" alt="" class="max-h-60 rounded-lg border border-zinc-200">
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('foto.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
        </div>
    </form>
</x-card>

@push('scripts')
<script>
document.getElementById('inputGambar').addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewWrap').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
@endsection
