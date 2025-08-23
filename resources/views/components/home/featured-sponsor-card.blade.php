@props([
    'href' => '',
    'sponsorName' => '',
    'tagline' => '',
    'description' => '',
    'title' => null,
])

@php
    $computedTitle = $title ?? "Learn more about {$sponsorName}";
@endphp

<a
    href="{{ $href }}"
    title="{{ $computedTitle }}"
    aria-label="Visit {{ $sponsorName }} website"
    target="_blank"
    rel="sponsored"
    class="group flex flex-col rounded-2xl bg-white/50 p-6 transition duration-200 hover:bg-white/70 hover:shadow-lg hover:shadow-gray-200/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:hover:shadow-transparent"
>
    <div>{{ $logo }}</div>

    <h3 class="sr-only">
        {{ $sponsorName }}
    </h3>

    <div class="mt-2.5 text-lg font-medium text-gray-800 dark:text-white">
        {{ $tagline }}
    </div>

    <p class="mt-1 text-gray-600 dark:text-zinc-400">
        {{ $description }}
    </p>
</a>
