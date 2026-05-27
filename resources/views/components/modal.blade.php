@props([
    'id',
    'title' => 'Modal',
    'size' => 'md',
])

@php
    $sizes = [
        'sm' => 'max-w-md',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-3xl',
    ];
    $sz = $sizes[$size] ?? $sizes['md'];
@endphp

<div id="{{ $id }}"
     class="hidden fixed inset-0 z-50 items-end justify-center sm:items-center"
     aria-modal="true" role="dialog"
     data-modal>

    <div class="absolute inset-0 bg-zinc-900/50" data-modal-overlay></div>

    <div class="relative w-full sm:w-auto {{ $sz }} mx-auto sm:my-8">
        <div class="bg-white rounded-t-2xl sm:rounded-2xl shadow-xl max-h-[92vh] flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-200">
                <h3 class="text-base font-semibold text-zinc-900">{{ $title }}</h3>
                <button type="button" class="text-zinc-500 hover:text-zinc-800 p-1 -m-1" data-modal-close aria-label="Tutup">
                    <x-icon name="close" class="w-5 h-5" />
                </button>
            </div>
            <div class="px-5 py-5 overflow-y-auto">
                {{ $slot }}
            </div>
            @isset($footer)
                <div class="px-5 py-4 border-t border-zinc-200 bg-zinc-50/60 rounded-b-2xl flex flex-wrap gap-2 justify-end">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
