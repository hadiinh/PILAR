@props([
    'user' => null,
    'showHeader' => true,
])

@php
    $u = $user ?? auth()->user();
@endphp

<div class="alamat-fields space-y-4"
     data-current-provinsi-id="{{ old('provinsi_id', $u?->provinsi_id) }}"
     data-current-provinsi-nama="{{ old('provinsi_nama', $u?->provinsi_nama) }}"
     data-current-kota-id="{{ old('kota_id', $u?->kota_id) }}"
     data-current-kota-nama="{{ old('kota_nama', $u?->kota_nama) }}"
     data-current-kecamatan-id="{{ old('kecamatan_id', $u?->kecamatan_id) }}"
     data-current-kecamatan-nama="{{ old('kecamatan_nama', $u?->kecamatan_nama) }}"
     data-current-kelurahan-id="{{ old('kelurahan_id', $u?->kelurahan_id) }}"
     data-current-kelurahan-nama="{{ old('kelurahan_nama', $u?->kelurahan_nama) }}">

    @if($showHeader)
        <div class="pb-1 border-b border-zinc-200">
            <h3 class="text-sm font-semibold text-zinc-900">Alamat</h3>
            <p class="text-xs text-zinc-500">Pilih wilayah sesuai data kependudukan Anda.</p>
        </div>
    @endif

    {{-- Status loading global --}}
    <p data-wilayah-status class="hidden text-xs text-zinc-500 flex items-center gap-2">
        <span class="inline-block w-3 h-3 rounded-full border-2 border-brand-500 border-t-transparent animate-spin"></span>
        <span data-wilayah-status-text>Memuat data wilayah…</span>
    </p>

    {{-- Pesan error --}}
    <p data-wilayah-error class="hidden text-xs text-red-600"></p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="space-y-1.5">
            <label for="provinsi_id" class="block text-sm font-semibold text-zinc-800">Provinsi <span class="text-red-600">*</span></label>
            <select id="provinsi_id" name="provinsi_id" data-wilayah="provinsi" required
                    class="block w-full h-11 px-3.5 rounded-lg border bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                <option value="">— Pilih Provinsi —</option>
            </select>
            <input type="hidden" name="provinsi_nama" data-wilayah-nama="provinsi" value="{{ old('provinsi_nama', $u?->provinsi_nama) }}">
            @error('provinsi_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-1.5">
            <label for="kota_id" class="block text-sm font-semibold text-zinc-800">Kota / Kabupaten <span class="text-red-600">*</span></label>
            <select id="kota_id" name="kota_id" data-wilayah="kota" required disabled
                    class="block w-full h-11 px-3.5 rounded-lg border bg-zinc-50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                <option value="">— Pilih Kota / Kabupaten —</option>
            </select>
            <input type="hidden" name="kota_nama" data-wilayah-nama="kota" value="{{ old('kota_nama', $u?->kota_nama) }}">
            @error('kota_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-1.5">
            <label for="kecamatan_id" class="block text-sm font-semibold text-zinc-800">Kecamatan <span class="text-red-600">*</span></label>
            <select id="kecamatan_id" name="kecamatan_id" data-wilayah="kecamatan" required disabled
                    class="block w-full h-11 px-3.5 rounded-lg border bg-zinc-50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                <option value="">— Pilih Kecamatan —</option>
            </select>
            <input type="hidden" name="kecamatan_nama" data-wilayah-nama="kecamatan" value="{{ old('kecamatan_nama', $u?->kecamatan_nama) }}">
            @error('kecamatan_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="space-y-1.5">
            <label for="kelurahan_id" class="block text-sm font-semibold text-zinc-800">Kelurahan / Desa <span class="text-red-600">*</span></label>
            <select id="kelurahan_id" name="kelurahan_id" data-wilayah="kelurahan" required disabled
                    class="block w-full h-11 px-3.5 rounded-lg border bg-zinc-50 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                <option value="">— Pilih Kelurahan / Desa —</option>
            </select>
            <input type="hidden" name="kelurahan_nama" data-wilayah-nama="kelurahan" value="{{ old('kelurahan_nama', $u?->kelurahan_nama) }}">
            @error('kelurahan_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <x-input name="alamat_detail"
             label="Alamat Detail"
             :value="old('alamat_detail', $u?->alamat_detail)"
             placeholder="Nama jalan, gang, patokan…" />

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <x-input name="rt" label="RT" :value="old('rt', $u?->rt)" placeholder="01" />
        <x-input name="rw" label="RW" :value="old('rw', $u?->rw ?? '016')" placeholder="016" />
        <x-input name="no_rumah" label="No. Rumah" :value="old('no_rumah', $u?->no_rumah)" placeholder="12A" />
        <x-input name="kode_pos" label="Kode Pos" :value="old('kode_pos', $u?->kode_pos)" placeholder="40534" />
    </div>
</div>
