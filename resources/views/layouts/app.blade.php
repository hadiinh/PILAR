@php
    $user = auth()->user();
    $isManager = $user && in_array($user->role, ['ketua_rw', 'admin']);
    $current = request()->path();
    $isActive = function (...$prefixes) use ($current) {
        foreach ($prefixes as $p) {
            if ($current === $p || str_starts_with($current, $p.'/')) return true;
        }
        return false;
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#245644">
    <title>@yield('title', 'PILAR RW 016') · PILAR RW 016</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/wilayah.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">

<a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 bg-white border border-zinc-300 rounded-lg px-3 py-2 text-sm">Lewati ke konten</a>

<div class="lg:flex">

    {{-- Sidebar (desktop) --}}
    @auth
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white border-r border-zinc-200">
        <div class="px-5 py-5 border-b border-zinc-200">
            <a href="{{ url('/beranda') }}" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold">P</span>
                <span class="flex flex-col leading-tight">
                    <span class="font-semibold text-zinc-900">PILAR</span>
                    <span class="text-xs text-zinc-500">RW 016 — Melong</span>
                </span>
            </a>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
                $items = [
                    ['label' => 'Beranda',     'href' => '/beranda',    'icon' => 'home',     'active' => $isActive('beranda')],
                    ['label' => 'Dashboard',   'href' => '/dashboard',  'icon' => 'building', 'active' => $isActive('dashboard'), 'show' => $isManager],
                    ['label' => 'Jadwal',      'href' => '/jadwal',     'icon' => 'calendar', 'active' => $isActive('jadwal')],
                    ['label' => 'Kegiatan',    'href' => '/kegiatan',   'icon' => 'megaphone','active' => $isActive('kegiatan')],
                    ['label' => 'Foto',        'href' => '/foto',       'icon' => 'image',    'active' => $isActive('foto')],
                    ['label' => 'Keuangan',    'href' => '/keuangan',   'icon' => 'wallet',   'active' => $isActive('keuangan')],
                    ['label' => 'Laporan',     'href' => '/laporan',    'icon' => 'flag',     'active' => $isActive('laporan')],
                    ['heading' => 'Manajemen', 'show' => $isManager],
                    ['label' => 'Manajemen Warga','href' => '/warga',   'icon' => 'users',    'active' => $isActive('warga'),       'show' => $isManager],
                    ['label' => 'Keluarga',    'href' => '/keluarga',   'icon' => 'users',    'active' => $isActive('keluarga'),    'show' => $isManager],
                    ['label' => 'Statistik',   'href' => '/statistik',  'icon' => 'building', 'active' => $isActive('statistik'),   'show' => $isManager],
                    ['label' => 'Pengajuan Akun','href' => '/pengajuan','icon' => 'clock',    'active' => $isActive('pengajuan'),   'show' => $isManager],
                    ['label' => 'Notifikasi',  'href' => '/notifikasi', 'icon' => 'message',  'active' => $isActive('notifikasi'),  'show' => $isManager],
                ];
            @endphp
            @foreach($items as $it)
                @if(isset($it['heading']))
                    @if(!isset($it['show']) || $it['show'])
                        <p class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-400">{{ $it['heading'] }}</p>
                    @endif
                @elseif(!isset($it['show']) || $it['show'])
                    <a href="{{ url($it['href']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ $it['active'] ? 'bg-brand-50 text-brand-800' : 'text-zinc-700 hover:bg-zinc-100' }}">
                        <x-icon :name="$it['icon']" class="w-5 h-5 {{ $it['active'] ? 'text-brand-700' : 'text-zinc-500' }}" />
                        {{ $it['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="px-3 py-3 border-t border-zinc-200">
            <button type="button"
                    onclick="document.dispatchEvent(new CustomEvent('open-profile-modal'))"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-zinc-100 text-left">
                <span class="w-9 h-9 rounded-full bg-zinc-200 text-zinc-700 flex items-center justify-center font-semibold">
                    {{ strtoupper(mb_substr($user->name ?? '?', 0, 1)) }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block text-sm font-semibold text-zinc-900 truncate">{{ $user->name }}</span>
                    <span class="block text-xs text-zinc-500 truncate">{{ ucwords(str_replace('_',' ', $user->role)) }}</span>
                </span>
            </button>
        </div>
    </aside>
    @endauth

    {{-- Main column --}}
    <div class="@auth lg:pl-64 @endauth flex-1 min-w-0">

        {{-- Top bar (mobile) --}}
        @auth
        <header class="lg:hidden sticky top-0 z-30 bg-white border-b border-zinc-200">
            <div class="px-4 h-14 flex items-center justify-between">
                <a href="{{ url('/beranda') }}" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold text-sm">P</span>
                    <span class="font-semibold text-zinc-900">PILAR RW 016</span>
                </a>
                <button type="button"
                        onclick="document.dispatchEvent(new CustomEvent('open-profile-modal'))"
                        class="w-9 h-9 rounded-full bg-zinc-100 text-zinc-700 flex items-center justify-center font-semibold"
                        aria-label="Profil">
                    {{ strtoupper(mb_substr($user->name ?? '?', 0, 1)) }}
                </button>
            </div>
        </header>
        @endauth

        <main id="main" class="@auth pb-24 lg:pb-10 @endauth">
            <div class="container-page py-5 md:py-8">
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- Bottom navigation (mobile only, authed) --}}
@auth
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-zinc-200 safe-bottom" aria-label="Navigasi bawah">
    @php
        $bottom = [
            ['label' => 'Beranda',  'href' => '/beranda',  'icon' => 'home',     'active' => $isActive('beranda')],
            ['label' => 'Jadwal',   'href' => '/jadwal',   'icon' => 'calendar', 'active' => $isActive('jadwal')],
            ['label' => 'Foto',     'href' => '/foto',     'icon' => 'image',    'active' => $isActive('foto')],
            ['label' => 'Keuangan', 'href' => '/keuangan', 'icon' => 'wallet',   'active' => $isActive('keuangan')],
            ['label' => 'Laporan',  'href' => '/laporan',  'icon' => 'flag',     'active' => $isActive('laporan')],
        ];
    @endphp
    <div class="grid grid-cols-5">
        @foreach($bottom as $it)
            <a href="{{ url($it['href']) }}"
               class="flex flex-col items-center justify-center gap-1 py-2 text-[11px] font-medium {{ $it['active'] ? 'text-brand-700' : 'text-zinc-500' }}">
                <x-icon :name="$it['icon']" class="w-5 h-5" />
                <span>{{ $it['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>

{{-- Profile modal (shared) --}}
@include('partials.profile-modal')
@endauth

<script>
    // Generic modal helper
    (function () {
        function openModal(el)  {
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(el) {
            if (!el) return;
            el.classList.add('hidden');
            el.classList.remove('flex');
            document.body.style.overflow = '';
        }
        window.PILAR = window.PILAR || {};
        window.PILAR.openModal = function (id) { openModal(document.getElementById(id)); };
        window.PILAR.closeModal = function (id) { closeModal(document.getElementById(id)); };

        document.addEventListener('click', function (e) {
            const open = e.target.closest('[data-open-modal]');
            if (open) { e.preventDefault(); openModal(document.getElementById(open.dataset.openModal)); return; }
            const close = e.target.closest('[data-modal-close]');
            if (close) { closeModal(close.closest('[data-modal]')); return; }
            const overlay = e.target.closest('[data-modal-overlay]');
            if (overlay) { closeModal(overlay.closest('[data-modal]')); return; }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-modal]').forEach(function (m) {
                    if (!m.classList.contains('hidden')) closeModal(m);
                });
            }
        });

        document.addEventListener('open-profile-modal', function () { openModal(document.getElementById('profileModal')); });
    })();
</script>

@stack('scripts')
</body>
</html>
