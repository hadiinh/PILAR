@props(['variant' => 'info', 'title' => null])

@php
    $variants = [
        'info'    => ['box' => 'bg-sky-50 border-sky-200 text-sky-900', 'icon' => 'info'],
        'success' => ['box' => 'bg-emerald-50 border-emerald-200 text-emerald-900', 'icon' => 'check-circle'],
        'warning' => ['box' => 'bg-amber-50 border-amber-200 text-amber-900', 'icon' => 'alert'],
        'danger'  => ['box' => 'bg-red-50 border-red-200 text-red-900', 'icon' => 'alert'],
    ];
    $v = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "border rounded-lg px-4 py-3 flex gap-3 items-start text-sm {$v['box']}"]) }}>
    <x-icon :name="$v['icon']" class="w-5 h-5 mt-0.5 shrink-0" />
    <div class="flex-1">
        @if($title)<p class="font-semibold mb-0.5">{{ $title }}</p>@endif
        <div>{{ $slot }}</div>
    </div>
</div>
