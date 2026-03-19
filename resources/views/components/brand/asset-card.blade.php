@props([
    'src' => '',
    'alt' => '',
    'downloadHref' => null,
    'height' => 'h-8',
    'isDarkSurface' => false,
])

@php
    $downloadHref = $downloadHref ?? $src;
    $containerClasses = [
        'grid h-50 w-full place-items-center rounded-xl p-5 ring-1',
        $isDarkSurface ? 'bg-gray-900 ring-gray-800' : 'bg-gray-100 ring-gray-300',
    ];
@endphp

<div
    x-data="{ isHovered: false }"
    class="flex flex-col items-center gap-5"
>
    {{-- Asset --}}
    <div class="{{ implode(' ', $containerClasses) }}">
        <img
            src="{{ $src }}"
            alt="{{ $alt }}"
            loading="lazy"
            class="{{ $height }} block transition duration-200 will-change-transform"
            x-bind:class="{ 'scale-105': isHovered }"
        />
    </div>

    {{-- Download button --}}
    <a
        download
        href="{{ $downloadHref }}"
        class="inline-flex items-center gap-2 rounded-xl bg-white py-3 pr-5 pl-3.5 text-sm font-medium ring-1 ring-gray-300 transition duration-200 ring-inset hover:bg-gray-100 dark:bg-cloud/60 dark:text-white dark:ring-transparent dark:hover:bg-cloud"
        x-on:mouseenter="isHovered = true"
        x-on:mouseleave="isHovered = false"
    >
        <x-icons.download class="size-5" />
        <div>Download</div>
    </a>
</div>
