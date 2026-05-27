@props([
    'name',
    'label' => null,
    'value' => null,
    'hint' => null,
    'required' => false,
    'placeholder' => '',
    'rows' => 4,
])

@php
    $id = $attributes->get('id') ?? $name;
    $val = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-zinc-800">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    <textarea id="{{ $id }}"
              name="{{ $name }}"
              rows="{{ $rows }}"
              placeholder="{{ $placeholder }}"
              @if($required) required @endif
              {{ $attributes->merge(['class' => 'block w-full px-3.5 py-2.5 rounded-lg border bg-white text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 ' . ($hasError ? 'border-red-400' : 'border-zinc-300')]) }}>{{ $val }}</textarea>

    @if($hint && !$hasError)
        <p class="text-xs text-zinc-500">{{ $hint }}</p>
    @endif
    @if($hasError)
        <p class="text-xs text-red-600">{{ $errors->first($name) }}</p>
    @endif
</div>
