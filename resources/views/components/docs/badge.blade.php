@props([
    'label',
    'tooltip' => null,
    'href' => null,
    'variant' => 'neutral',
])

@php
    $palettes = [
        'neutral' => 'bg-gray-100 text-gray-600 ring-gray-200 dark:bg-white/10 dark:text-gray-300 dark:ring-white/15',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/25',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-400/10 dark:text-amber-300 dark:ring-amber-400/25',
        'danger' => 'bg-rose-50 text-rose-700 ring-rose-200 dark:bg-rose-400/10 dark:text-rose-300 dark:ring-rose-400/25',
        'jump' => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-400/10 dark:text-indigo-300 dark:ring-indigo-400/25',
    ];

    // not-prose: keeps the typography plugin from restyling the pill in markdown.
    $classes = implode(' ', [
        'not-prose inline-flex select-none items-center whitespace-nowrap rounded-full',
        'px-1.5 py-0.5 align-middle text-[11px] font-medium leading-4 no-underline',
        'ring-1 ring-inset transition',
        $palettes[$variant] ?? $palettes['neutral'],
    ]);
@endphp

@if (filled($href))
    <a
        href="{{ $href }}"
        class="{{ $classes }} hover:ring-2"
        @if (filled($tooltip))
            title="{{ $tooltip }}"
            aria-label="{{ $tooltip }}"
        @endif
    >{{ $label }}</a>
@else
    <span
        class="{{ $classes }}"
        @if (filled($tooltip))
            title="{{ $tooltip }}"
            aria-label="{{ $tooltip }}"
        @endif
    >{{ $label }}</span>
@endif
