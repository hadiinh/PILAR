@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'required' => false,
    'placeholder' => '',
    'rupiah' => false,
])

@php
    $id = $attributes->get('id') ?? $name;
    $val = old($name, $value);
    $hasError = $errors->has($name);

    if ($rupiah) {
        $type = 'text';
        $attributes = $attributes->merge(['data-rupiah' => '', 'inputmode' => 'numeric']);
        if ($val !== null && $val !== '') {
            $val = number_format((int) preg_replace('/\D/', '', (string) $val), 0, ',', '.');
        }
    }

    $isPassword = $type === 'password';
    $inputClass = ($attributes->get('class') ? $attributes->get('class') . ' ' : '')
        . 'block w-full h-11 px-3.5 rounded-lg border bg-white text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 '
        . ($isPassword ? 'pr-11 ' : '')
        . ($hasError ? 'border-red-400' : 'border-zinc-300');
@endphp

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-semibold text-zinc-800">
            {{ $label }}
            @if($required)<span class="text-red-600">*</span>@endif
        </label>
    @endif

    @if($isPassword)<div class="relative">@endif
    <input id="{{ $id }}"
           type="{{ $type }}"
           name="{{ $name }}"
           value="{{ $val }}"
           placeholder="{{ $placeholder }}"
           @if($required) required @endif
           class="{{ $inputClass }}"
           {{ $attributes->except(['class']) }}>

    @if($isPassword)
        <button type="button"
                data-pw-toggle="{{ $id }}"
                aria-label="Tampilkan kata sandi"
                aria-pressed="false"
                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-zinc-400 hover:text-zinc-700 focus:outline-none">
            <x-icon name="eye" data-pw-icon="eye" class="w-5 h-5" />
            <x-icon name="eye-off" data-pw-icon="eye-off" class="w-5 h-5 hidden" />
        </button>
    </div>
    @endif

    @if($hint && !$hasError)
        <p class="text-xs text-zinc-500">{{ $hint }}</p>
    @endif
    @if($hasError)
        <p class="text-xs text-red-600">{{ $errors->first($name) }}</p>
    @endif
</div>