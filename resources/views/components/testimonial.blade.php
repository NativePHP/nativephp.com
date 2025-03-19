@props([
    'quote',
    'author',
    'handle',
    'content',
    'avatar',
])

@php
    // Define color palettes with light and dark variants using Tailwind classes
    $colorPalettes = [
        ['bg' => 'bg-cyan-100 dark:bg-cyan-900/20', 'text' => 'text-cyan-800 dark:text-cyan-200'],
        ['bg' => 'bg-amber-100 dark:bg-amber-900/20', 'text' => 'text-amber-800 dark:text-amber-200'],
        ['bg' => 'bg-red-100 dark:bg-red-900/20', 'text' => 'text-red-800 dark:text-red-200'],
        ['bg' => 'bg-green-100 dark:bg-green-900/20', 'text' => 'text-green-800 dark:text-green-200'],
        ['bg' => 'bg-blue-100 dark:bg-blue-900/20', 'text' => 'text-blue-800 dark:text-blue-200'],
        ['bg' => 'bg-purple-100 dark:bg-purple-900/20', 'text' => 'text-purple-800 dark:text-purple-200'],
        ['bg' => 'bg-orange-100 dark:bg-orange-900/20', 'text' => 'text-orange-800 dark:text-orange-200'],
        ['bg' => 'bg-teal-100 dark:bg-teal-900/20', 'text' => 'text-teal-800 dark:text-teal-200'],
        ['bg' => 'bg-slate-100 dark:bg-slate-800/40', 'text' => 'text-slate-800 dark:text-slate-200'],
        ['bg' => 'bg-violet-100 dark:bg-violet-900/20', 'text' => 'text-violet-800 dark:text-violet-200'],
    ];

    // Select a random color palette
    $randomPalette = $colorPalettes[array_rand($colorPalettes)];
@endphp

<div
    x-ref="testimonial"
    class="mt-5 inline-block rounded-2xl p-6 ring-1 ring-black/10 dark:bg-gray-900/40 dark:ring-white/10"
>
    {{-- Highlight --}}
    <div
        class="{{ $randomPalette['bg'] }} {{ $randomPalette['text'] }} rounded-xl p-4 text-center font-medium transition-colors"
    >
        "{{ $quote }}"
    </div>

    {{-- Author --}}
    <div class="flex items-center gap-2.5 py-3.5">
        {{-- Image --}}
        <img
            src="{{ $avatar }}"
            alt="{{ $author }}"
            class="size-11 rounded-full"
            loading="lazy"
        />

        {{-- Information --}}
        <div>
            {{-- Name --}}
            <div class="dark:text-gray-100">{{ $author }}</div>

            @if ($handle)
                {{-- Handle --}}
                <div class="text-sm text-gray-400 dark:text-gray-400">
                    {{ $handle }}
                </div>
            @endif
        </div>
    </div>

    {{-- Content --}}
    @if ($content)
        <p class="text-sm dark:text-gray-300">
            {{ $content }}
        </p>
    @endif
</div>
