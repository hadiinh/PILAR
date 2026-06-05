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

        <label class="flex items-start gap-3 p-3 rounded-lg border border-zinc-200">
            <input type="checkbox" name="is_kepala_keluarga" value="1"
                   @checked(old('is_kepala_keluarga', $u?->is_kepala_keluarga))
                   class="mt-0.5 w-4 h-4 text-brand-700 focus:ring-brand-500 rounded">
            <span class="text-sm">
                <span class="font-semibold text-zinc-900 block">Tandai sebagai Kepala Keluarga</span>
                <span class="text-zinc-500 block text-xs mt-0.5">Akan ditampilkan di halaman daftar keluarga.</span>
            </span>
        </label>
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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label for="role" class="block text-sm font-semibold text-zinc-800">Peran <span class="text-red-600">*</span></label>
                <select id="role" name="role" required
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @foreach(['user'=>'Warga','ketua_rw'=>'Ketua RW','admin'=>'Admin'] as $v => $lbl)
                        <option value="{{ $v }}" @selected(old('role', $u?->role ?? 'user') === $v)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5">
                <label for="status_warga" class="block text-sm font-semibold text-zinc-800">Status Warga</label>
                <select id="status_warga" name="status_warga"
                        class="block w-full h-11 px-3.5 rounded-lg border border-zinc-300 bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                    @foreach(['tetap'=>'Warga Tetap','kontrak'=>'Kontrak','kos'=>'Kos','lainnya'=>'Lainnya'] as $v => $lbl)
                        <option value="{{ $v }}" @selected(old('status_warga', $u?->status_warga ?? 'tetap') === $v)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if(!$u)
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                <p class="text-sm text-blue-900">
                    <span class="font-semibold">Kata sandi awal akan dibuatkan otomatis</span> dan dikirimkan ke nomor WhatsApp warga. 
                    Warga wajib mengubah kata sandinya saat login pertama kali.
                </p>
            </div>
        @endif
    </div>

    {{-- Alamat --}}
    @include('partials.alamat-fields', ['user' => $u, 'showHeader' => true])
</div>
