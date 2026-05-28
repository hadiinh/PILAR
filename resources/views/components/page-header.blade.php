@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-5 sm:mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3']) }}>
    <div class="min-w-0">
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-zinc-900 break-words">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-1 text-sm text-zinc-500">{{ $subtitle }}</p>
        @endif
    </div>
    @if(trim($slot))
        <div class="flex flex-wrap gap-2 sm:justify-end [&>*]:flex-1 sm:[&>*]:flex-none">{{ $slot }}</div>
    @endif
</div>
