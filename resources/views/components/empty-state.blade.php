@props([
    'icon' => 'info',
    'title' => 'Belum ada data',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4']) }}>
    <div class="mx-auto w-14 h-14 rounded-full bg-zinc-100 flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="w-7 h-7 text-zinc-500" />
    </div>
    <h3 class="font-semibold text-zinc-900">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-zinc-500 mt-1 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    @if(trim($slot))
        <div class="mt-5 flex justify-center">{{ $slot }}</div>
    @endif
</div>
