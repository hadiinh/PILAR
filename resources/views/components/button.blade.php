@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'block' => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-brand-500 disabled:opacity-60 disabled:cursor-not-allowed';

    $variants = [
        'primary'   => 'bg-brand-700 text-white hover:bg-brand-800',
        'secondary' => 'bg-white text-zinc-800 border border-zinc-300 hover:bg-zinc-50',
        'subtle'    => 'bg-zinc-100 text-zinc-800 hover:bg-zinc-200',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700',
        'warning'   => 'bg-amber-500 text-white hover:bg-amber-600',
        'ghost'     => 'bg-transparent text-zinc-700 hover:bg-zinc-100',
        'link'      => 'bg-transparent text-brand-700 hover:underline rounded-none px-0',
    ];

    $sizes = [
        'sm' => 'text-sm h-9 px-3',
        'md' => 'text-sm h-11 px-4',
        'lg' => 'text-base h-12 px-5',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']).($block ? ' w-full' : '');
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
    </button>
@endif
