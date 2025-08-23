@props([
    'href' => '',
    'partnerName' => '',
    'tagline' => '',
    'description' => '',
    'title' => null,
])

@php
    $computedTitle = $title ?? "Learn more about {$partnerName}";
@endphp

<a
    href="{{ $href }}"
    title="{{ $computedTitle }}"
    aria-label="Visit {{ $partnerName }} website"
    target="_blank"
    rel="sponsored"
    class="dark:hover:ring-cloud/70 flex flex-col rounded-2xl bg-white/50 p-7 text-pretty transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-white/70 hover:shadow-lg hover:ring-1 hover:shadow-gray-200/70 hover:ring-black/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 dark:bg-slate-950/30 dark:hover:bg-slate-950/80 dark:hover:shadow-transparent"
>
    <div class="grid h-15 w-35 place-items-center">{{ $logo }}</div>

    <h3 class="sr-only">
        {{ $partnerName }}
    </h3>

    <div class="mt-5 font-medium text-gray-800 dark:text-white">
        {{ $tagline }}
    </div>

    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
        {{ $description }}
    </p>
</a>
