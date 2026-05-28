@php $u = auth()->user(); @endphp
<x-modal id="profileModal" title="Profil Saya" size="lg">
    <div class="space-y-5">
        <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-lg border border-zinc-200">
            <span class="w-12 h-12 rounded-full bg-brand-700 text-white flex items-center justify-center font-semibold text-lg">
                {{ strtoupper(mb_substr($u->name ?? '?', 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="font-semibold text-zinc-900 truncate">{{ $u->name }}</p>
                <p class="text-sm text-zinc-500 truncate">{{ $u->email }}</p>
                <x-badge variant="brand" class="mt-1">{{ ucwords(str_replace('_',' ', $u->role)) }}</x-badge>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Data diri --}}
            <div class="space-y-4">
                <div class="pb-1 border-b border-zinc-200">
                    <h3 class="text-sm font-semibold text-zinc-900">Data Diri</h3>
                </div>

                <x-input name="name" label="Nama Lengkap" :value="$u->name" required />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <x-input name="no_hp"
                             type="tel"
                             label="Nomor HP / WhatsApp"
                             :value="$u->no_hp"
                             placeholder="08123456789"
                             hint="Digunakan untuk notifikasi WhatsApp." />

                    <div class="space-y-1.5">
                        <label for="status_warga" class="block text-sm font-semibold text-zinc-800">Status Warga</label>
                        <select id="status_warga" name="status_warga"
                                class="block w-full h-11 px-3.5 rounded-lg border bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 border-zinc-300">
                            @foreach(['tetap' => 'Warga Tetap', 'kontrak' => 'Kontrak', 'kos' => 'Kos', 'lainnya' => 'Lainnya'] as $v => $label)
                                <option value="{{ $v }}" @selected(($u->status_warga ?? 'tetap') === $v)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <label class="flex items-start gap-3 p-3 rounded-lg border border-zinc-200">
                    <input type="checkbox" name="notif_wa_aktif" value="1"
                           @checked($u->notif_wa_aktif)
                           class="mt-0.5 w-4 h-4 text-brand-700 focus:ring-brand-500 rounded">
                    <span class="text-sm">
                        <span class="font-semibold text-zinc-900 block">Aktifkan Notifikasi WhatsApp</span>
                        <span class="text-zinc-500 block text-xs mt-0.5">Anda akan menerima pemberitahuan jadwal, kegiatan, dan status laporan.</span>
                    </span>
                </label>
            </div>

            {{-- Alamat --}}
            @include('partials.alamat-fields', ['user' => $u, 'showHeader' => true])

            <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
                <x-button type="button" variant="secondary" data-modal-close block>Tutup</x-button>
                <x-button type="submit" variant="primary" icon="check" block>Simpan Perubahan</x-button>
            </div>
        </form>

        <details class="rounded-lg border border-zinc-200">
            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold text-zinc-800">Ubah Kata Sandi</summary>
            <form action="{{ route('profile.changePassword') }}" method="POST" class="p-3 space-y-3 border-t border-zinc-200">
                @csrf
                <x-input name="password_lama" type="password" label="Kata Sandi Lama" required />
                <x-input name="password_baru" type="password" label="Kata Sandi Baru" hint="Minimal 6 karakter" required />
                <x-input name="password_baru_confirmation" type="password" label="Ulangi Kata Sandi Baru" required />
                <x-button type="submit" variant="primary" icon="check" block>Ubah Kata Sandi</x-button>
            </form>
        </details>

        <form action="{{ url('/logout') }}" method="POST" class="pt-2 border-t border-zinc-200">
            @csrf
            <x-button type="submit" variant="danger" icon="logout" block>Keluar</x-button>
        </form>
    </div>
</x-modal>
