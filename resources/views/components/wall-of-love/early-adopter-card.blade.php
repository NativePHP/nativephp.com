@props([
    'name' => '',
    'title' => '',
    'url' => '',
    'image' => '',
    'featured' => false,
])

<div
    class="group rounded-xl text-center transition duration-300 ease-out will-change-transform hover:scale-102"
>
    {{-- Image --}}
    <img
        src="{{ $image }}"
        alt="{{ $name }}"
        loading="lazy"
        width="229"
        height="275"
        class="aspect-[1/1.2] rounded-3xl object-cover brightness-90 transition duration-300 group-hover:brightness-100"
    />

    {{-- Name & Title --}}
    <div
        class="mt-2 transition duration-300 ease-out will-change-transform group-hover:translate-y-0.5"
    >
        <h3 class="truncate capitalize">
            {{ $name }}
        </h3>
        <h4 class="truncate text-sm capitalize opacity-50">
            {{ $title }}
        </h4>
    </div>

    {{-- External link --}}
    @if ($url)
        <a
            href="{{ $url }}"
            target="_blank"
            rel="nofollow noopener noreferrer"
            class="group/link absolute top-3 right-3 flex items-center gap-2 rounded-xl bg-white/70 px-3 py-1.5 backdrop-blur-sm transition duration-300 hover:bg-white/100 dark:bg-black/70 dark:hover:bg-black/100"
        >
            <div class="text-sm">Visit</div>
            <x-icons.right-arrow
                class="h-3 -rotate-45 transition duration-300 will-change-transform group-hover/link:translate-x-px group-hover/link:-translate-y-px"
            />
        </a>
    @endif
</div>
