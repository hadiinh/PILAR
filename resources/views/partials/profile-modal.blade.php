@php $u = auth()->user(); @endphp
<x-modal id="profileModal" title="Profil Saya" size="md">
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

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            <x-input name="name" label="Nama Lengkap" :value="$u->name" required />
            <div class="grid grid-cols-3 gap-3">
                <x-input name="rt" label="RT" :value="$u->rt" placeholder="01" />
                <x-input name="rw_display" label="RW" value="016" disabled class="bg-zinc-100" />
                <x-input name="no_rumah" label="No Rumah" :value="$u->no_rumah" placeholder="12" />
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-2 pt-2">
                <x-button type="button" variant="secondary" data-modal-close block>Tutup</x-button>
                <x-button type="submit" variant="primary" icon="check" block>Simpan</x-button>
            </div>
        </form>

        <form action="{{ url('/logout') }}" method="POST" class="pt-2 border-t border-zinc-200">
            @csrf
            <x-button type="submit" variant="danger" icon="logout" block>Keluar</x-button>
        </form>
    </div>
</x-modal>
