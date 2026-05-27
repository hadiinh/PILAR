@props(['padding' => 'p-5 md:p-6'])

<div {{ $attributes->merge(['class' => 'bg-white border border-zinc-200/80 rounded-2xl shadow-sm ' . $padding]) }}>
    {{ $slot }}
</div>
