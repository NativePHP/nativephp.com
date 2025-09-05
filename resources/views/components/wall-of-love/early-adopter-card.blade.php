@props([
    "name" => "",
    "title" => "",
    "url" => "",
    "image" => "",
    "featured" => false,
])

<div
    class="group mt-3 inline-block break-inside-avoid overflow-hidden rounded-2xl text-center transition duration-300 ease-out will-change-transform hover:scale-102 xl:mt-5"
>
    <div class="grid">
        {{-- Image --}}
        <img
            src="{{ $image }}"
            alt="{{ $name }}"
            loading="lazy"
            @if ($featured)
                width="229"
                height="275"
            @else
                width="229"
                height="120"
            @endif
            @class([
                "self-center justify-self-center object-cover brightness-80 transition duration-300 [grid-area:1/-1] group-hover:brightness-100",
                "aspect-[1/1.3] xl:aspect-[1/1.5]" => $featured,
                "aspect-square max-h-50 grayscale group-hover:grayscale-0 xl:max-h-none" => ! $featured,
            ])
        />

        {{-- Name & Title --}}
        <div
            @class([
                "relative z-0 w-full self-end justify-self-start bg-gradient-to-t px-4 pt-13 pb-4 text-white [grid-area:1/-1]",
                "from-blue-500 to-transparent" => $featured,
                "from-black to-transparent" => ! $featured,
            ])
        >
            <div
                class="transition duration-300 ease-out will-change-transform group-hover:-translate-y-0.5"
            >
                <h3
                    @class([
                        "truncate capitalize",
                        "text-lg font-medium" => $featured,
                    ])
                >
                    {{ $name }}
                </h3>
                <h4
                    @class([
                        "truncate capitalize opacity-50",
                        "text-sm" => $featured,
                        "text-xs" => ! $featured,
                    ])
                >
                    {{ $title }}
                </h4>
            </div>
        </div>

        {{-- External link --}}
        @if ($url)
            <a
                href="{{ $url }}"
                target="_blank"
                rel="nofollow noopener noreferrer"
                class="group/link mt-3 mr-3 flex items-center gap-2 self-start justify-self-end rounded-xl bg-white/70 px-3 py-1.5 opacity-0 backdrop-blur-sm transition duration-300 [grid-area:1/-1] group-hover:opacity-100 hover:bg-white/100 dark:bg-black/70 dark:hover:bg-black/100"
            >
                <div class="text-sm">Visit</div>
                <x-icons.right-arrow
                    class="h-3 -rotate-45 transition duration-300 will-change-transform group-hover/link:translate-x-px group-hover/link:-translate-y-px"
                />
            </a>
        @endif
    </div>
</div>
