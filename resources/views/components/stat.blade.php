@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'brand',
    'help' => null,
    'valueId' => null,
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

<div {{ $attributes->merge(['class' => 'bg-white border border-zinc-200/80 rounded-2xl p-3 sm:p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-2 sm:gap-3">
        <div class="min-w-0">
            <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-wide text-zinc-500 truncate">{{ $label }}</p>
            <p class="mt-1.5 sm:mt-2 text-lg sm:text-2xl font-bold text-zinc-900 truncate" @if($valueId) id="{{ $valueId }}" @endif>{{ $value }}</p>
            @if($help)
                <p class="mt-1 text-xs text-zinc-500 line-clamp-2">{{ $help }}</p>
            @endif
        </div>
        @if($icon)
            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg {{ $tc }} flex items-center justify-center shrink-0">
                <x-icon :name="$icon" class="w-4 h-4 sm:w-5 sm:h-5" />
            </div>
        @endif
    </div>
</div>
