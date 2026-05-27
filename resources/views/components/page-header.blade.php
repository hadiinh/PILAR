@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3']) }}>
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1 text-sm text-zinc-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(trim($slot))
        <div class="flex flex-wrap gap-2 sm:justify-end">{{ $slot }}</div>
    @endif
</div>
