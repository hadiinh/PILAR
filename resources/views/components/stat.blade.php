@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'brand',
    'help' => null,
])

@php
    $tones = [
        'brand'   => 'bg-brand-50 text-brand-700',
        'success' => 'bg-emerald-50 text-emerald-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger'  => 'bg-red-50 text-red-700',
        'info'    => 'bg-sky-50 text-sky-700',
        'neutral' => 'bg-zinc-100 text-zinc-700',
    ];
    $tc = $tones[$tone] ?? $tones['neutral'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-zinc-200/80 rounded-2xl p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold text-zinc-900 truncate">{{ $value }}</p>
            @if($help)
                <p class="mt-1 text-xs text-zinc-500">{{ $help }}</p>
            @endif
        </div>
        @if($icon)
            <div class="w-10 h-10 rounded-lg {{ $tc }} flex items-center justify-center shrink-0">
                <x-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif
    </div>
</div>
