@props([
    'quote',
    'author',
    'handle',
    'content',
    'avatar',
])

@php
    // Define color palettes for light mode only
    $lightColorPalettes = [
        ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-800'],
        ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
        ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
        ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
        ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
        ['bg' => 'bg-purple-100', 'text' => 'text-purple-800'],
        ['bg' => 'bg-orange-100', 'text' => 'text-orange-800'],
        ['bg' => 'bg-teal-100', 'text' => 'text-teal-800'],
        ['bg' => 'bg-slate-100', 'text' => 'text-slate-800'],
        ['bg' => 'bg-violet-100', 'text' => 'text-violet-800'],
    ];

    // Select a random light color palette
    $randomLightPalette = $lightColorPalettes[array_rand($lightColorPalettes)];

    // Define a single consistent dark mode color palette
    $darkBg = 'dark:bg-indigo-900/20';
    $darkText = 'dark:text-indigo-200';
@endphp

<div
    x-ref="testimonial"
    class="mt-5 inline-block rounded-2xl p-6 ring-1 ring-black/10 dark:bg-gray-900/40 dark:ring-white/10"
>
    {{-- Highlight --}}
    <div
        class="{{ $randomLightPalette['bg'] }} {{ $darkBg }} {{ $randomLightPalette['text'] }} {{ $darkText }} rounded-xl p-4 text-center font-medium transition-colors"
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
