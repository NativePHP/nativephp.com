@props([
    'href' => '',
    'partnerName' => '',
])

<a
    href="{{ $href }}"
    aria-label="Visit {{ $partnerName }} website"
    target="_blank"
    rel="sponsored"
    class="dark:hover:ring-cloud/70 grid h-20 place-items-center rounded-2xl bg-white/50 px-5 text-pretty transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-white/70 hover:shadow-lg hover:ring-1 hover:shadow-gray-200/70 hover:ring-black/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/60 dark:bg-slate-950/30 dark:hover:bg-slate-950/80 dark:hover:shadow-transparent"
>
    <div class="grid h-15 w-35 place-items-center">{{ $slot }}</div>

    <h3 class="sr-only">
        {{ $partnerName }}
    </h3>
</a>
