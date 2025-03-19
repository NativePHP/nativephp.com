@props([
    'quote',
    'author',
    'handle',
    'content',
    'avatar',
])

@php
    // Define color palettes
    $colorPalettes = [
        ['bg' => '#CEF8FF', 'text' => 'text-cyan-800'],
        ['bg' => '#FFF4CC', 'text' => 'text-amber-800'],
        ['bg' => '#FFE2E2', 'text' => 'text-red-800'],
        ['bg' => '#E2F7E1', 'text' => 'text-green-800'],
        ['bg' => '#E2E5FF', 'text' => 'text-blue-800'],
        ['bg' => '#F9E3FF', 'text' => 'text-purple-800'],
        ['bg' => '#FFEDD5', 'text' => 'text-orange-800'],
        ['bg' => '#E1F9F2', 'text' => 'text-teal-800'],
        ['bg' => '#F1F5F9', 'text' => 'text-slate-800'],
        ['bg' => '#F5F3FF', 'text' => 'text-violet-800'],
    ];

    // Select a random color palette
    $randomPalette = $colorPalettes[array_rand($colorPalettes)];
@endphp

<div
    x-ref="testimonial"
    class="mt-5 inline-block rounded-2xl p-6 ring-1 ring-black/10"
>
    {{-- Highlight --}}
    <div
        class="{{ $randomPalette['text'] }} rounded-xl p-4 text-center"
        style="background-color: {{ $randomPalette['bg'] }}"
    >
        "{{ $quote }}"
    </div>

    {{-- Author --}}
    <div class="flex items-center gap-2 py-3.5">
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
            <div>{{ $author }}</div>

            @if ($handle)
                {{-- Handle --}}
                <div class="text-sm text-gray-400">
                    {{ $handle }}
                </div>
            @endif
        </div>
    </div>

    {{-- Content --}}
    @if ($content)
        <p class="text-sm">
            {{ $content }}
        </p>
    @endif
</div>
