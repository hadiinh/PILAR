@props(['user' => null])
@php
    $u = $user;
@endphp

<div class="space-y-6">
    {{-- Identitas --}}
    <div class="space-y-4">
        <div class="pb-1 border-b border-zinc-200">
            <h3 class="text-sm font-semibold text-zinc-900">Identitas</h3>
            <p class="text-xs text-zinc-500">Data kependudukan warga.</p>
        </div>

        <x-input name="name" label="Nama Lengkap" :value="$u?->name" required />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-input name="nik" label="NIK" inputmode="numeric" maxlength="16" pattern="[0-9]{16}"
                     :value="$u?->nik" placeholder="16 digit NIK" />
            <x-input name="no_kk" label="Nomor KK" inputmode="numeric" maxlength="16"
                     :value="$u?->no_kk" placeholder="16 digit Nomor KK" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-input name="tempat_lahir" label="Tempat Lahir" :value="$u?->tempat_lahir" />
            <x-input name="tanggal_lahir" type="date" label="Tanggal Lahir"
                     :value="$u?->tanggal_lahir?->format('Y-m-d')" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="space-y-1.5">
                <label for="jenis_kelamin" class="block text-sm font-semibold text-zinc-800">Jenis Kelamin</label>
                <select id="jenis_kelamin" name="jenis_kelamin"
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="">— Pilih —</option>
                    <option value="L" @selected(old('jenis_kelamin', $u?->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $u?->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="status_keluarga" class="block text-sm font-semibold text-zinc-800">Status dalam Keluarga</label>
                <select id="status_keluarga" name="status_keluarga"
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="">— Pilih —</option>
                    @foreach(['kepala_keluarga'=>'Kepala Keluarga','istri'=>'Istri','anak'=>'Anak','lainnya'=>'Lainnya'] as $v => $lbl)
                        <option value="{{ $v }}" @selected(old('status_keluarga', $u?->status_keluarga) === $v)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <x-input name="pekerjaan" label="Pekerjaan" :value="$u?->pekerjaan" />
        </div>
    </div>

    {{-- Status Rumah --}}
    <div class="space-y-4">
        <div class="pb-1 border-b border-zinc-200">
            <h3 class="text-sm font-semibold text-zinc-900">Status Tinggal</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label for="status_warga" class="block text-sm font-semibold text-zinc-800">Status Warga</label>
                <select id="status_warga" name="status_warga"
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @foreach(['tetap'=>'Warga Tetap','kontrak'=>'Kontrak','kos'=>'Kos','lainnya'=>'Lainnya'] as $v => $lbl)
                        <option value="{{ $v }}" @selected(old('status_warga', $u?->status_warga ?? 'tetap') === $v)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="status_rumah" class="block text-sm font-semibold text-zinc-800">Status Rumah</label>
                <select id="status_rumah" name="status_rumah"
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    <option value="">— Pilih —</option>
                    <option value="tetap" @selected(old('status_rumah', $u?->status_rumah) === 'tetap')>Milik Sendiri (Tetap)</option>
                    <option value="kontrak" @selected(old('status_rumah', $u?->status_rumah) === 'kontrak')>Kontrak / Sewa</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Akun & Kontak --}}
    <div class="space-y-4">
        <div class="pb-1 border-b border-zinc-200">
            <h3 class="text-sm font-semibold text-zinc-900">Akun & Kontak</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <x-input name="no_hp" type="tel" label="Nomor HP / WhatsApp" :value="$u?->no_hp" />
            <x-input name="email" type="email" label="Email (opsional)" :value="$u?->email" />
        </div>
    </div>

    {{-- Alamat --}}
    @include('partials.alamat-fields', ['user' => $u, 'showHeader' => true])
</div>
