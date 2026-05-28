<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#245644">
    <title>@yield('title', 'PILAR RW 016')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased">

<div class="min-h-screen flex flex-col">
    <header class="bg-white border-b border-zinc-200">
        <div class="container-page h-14 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-brand-700 text-white flex items-center justify-center font-bold text-sm">P</span>
                <span class="font-semibold text-zinc-900">PILAR RW 016</span>
            </a>
            <span class="text-xs text-zinc-500 hidden sm:block">Pusat Informasi & Layanan RW</span>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center py-8">
        <div class="w-full container-page max-w-xl">
            @yield('content')
        </div>
    </main>

    @stack('scripts')

    <footer class="border-t border-zinc-200 bg-white">
        <div class="container-page py-4 text-xs text-zinc-500 text-center">
            &copy; {{ date('Y') }} PILAR RW 016 · Kelurahan Melong, Cimahi Selatan
        </div>
    </footer>
</div>

</body>
</html>
