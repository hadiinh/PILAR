@extends('layouts.app')

@section('title', 'Unggah Foto')

@section('content')
<x-page-header title="Unggah Foto" subtitle="Tambahkan dokumentasi kegiatan warga." />

<x-flash />

<x-card>
    <form action="{{ route('foto.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 max-w-2xl">
        @csrf

        <x-input name="judul" label="Judul Foto" placeholder="Contoh: Gotong Royong RT 02" required />

        <x-select name="kategori" label="Kategori" required>
            <option value="">— Pilih kategori —</option>
            @foreach(['Sosial', 'Kebersihan', 'Rapat', 'Olahraga', 'Lainnya'] as $k)
                <option value="{{ $k }}" {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
            @endforeach
        </x-select>

        <x-textarea name="deskripsi" label="Deskripsi (opsional)" rows="4" placeholder="Ceritakan secara singkat tentang foto ini." />

        <div>
            <label class="block text-sm font-semibold text-zinc-800 mb-1.5">Foto <span class="text-red-600">*</span></label>
            <input type="file" name="gambar" id="inputGambar" accept="image/*" required
                   class="block w-full text-sm text-zinc-700 file:mr-3 file:rounded-lg file:border-0 file:bg-zinc-100 file:px-4 file:py-2 file:text-sm file:font-semibold hover:file:bg-zinc-200">
            <p class="text-xs text-zinc-500 mt-1.5">JPG/PNG/WebP, maksimal 2 MB.</p>

            <div id="previewWrap" class="hidden mt-3">
                <p class="text-xs text-zinc-500 mb-1">Pratinjau</p>
                <img id="previewImg" alt="" class="max-h-60 max-w-full h-auto rounded-lg border border-zinc-200">
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
            <x-button href="{{ route('foto.index') }}" variant="secondary" block>Batal</x-button>
            <x-button type="submit" variant="primary" icon="check" block>Unggah Foto</x-button>
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
