@props([
    'name',
    'label' => null,
    'value' => null,
    'hint' => null,
    'required' => false,
])

@php
    $id = $attributes->get('id') ?? $name;
    $hasError = $errors->has($name);
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-zinc-800">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    <select id="{{ $id }}"
            name="{{ $name }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => 'block w-full h-11 px-3.5 rounded-lg border bg-white text-zinc-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 ' . ($hasError ? 'border-red-400' : 'border-zinc-300')]) }}>
        {{ $slot }}
    </select>

    @if($hint && !$hasError)
        <p class="text-xs text-zinc-500">{{ $hint }}</p>
    @endif
    @if($hasError)
        <p class="text-xs text-red-600">{{ $errors->first($name) }}</p>
    @endif
</div>
