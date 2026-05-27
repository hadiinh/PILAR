@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral'  => 'bg-zinc-100 text-zinc-700',
        'brand'    => 'bg-brand-100 text-brand-800',
        'success'  => 'bg-emerald-100 text-emerald-800',
        'warning'  => 'bg-amber-100 text-amber-800',
        'danger'   => 'bg-red-100 text-red-700',
        'info'     => 'bg-sky-100 text-sky-800',
    ];
    $cls = $variants[$variant] ?? $variants['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full $cls"]) }}>
    {{ $slot }}
</span>
