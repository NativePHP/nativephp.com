@props([
    'title' => '',
    'url' => '#',
    'date' => null,
])

<a
    href="{{ $url }}"
    class="block"
    aria-labelledby="article-title-{{ Str::slug($title) }}"
>
    <article
        class="group relative z-0 block overflow-hidden rounded-2xl bg-zinc-200/60 p-7 transition duration-300 hover:bg-violet-200/60 dark:bg-mirage dark:hover:bg-violet-500/10"
    >
        {{-- Header --}}
        <div class="flex items-start justify-between gap-10">
            {{-- Title --}}
            <h3
                id="article-title-{{ Str::slug($title) }}"
                class="line-clamp-4 max-w-xs text-xl leading-relaxed font-semibold"
            >
                {{ $title }}
            </h3>

            {{-- Arrow --}}
            <x-icons.right-arrow
                class="mt-2 -ml-5 size-3.5 shrink-0 transition duration-300 will-change-transform group-hover:translate-x-1"
                aria-hidden="true"
            />
        </div>

        <div class="flex items-end justify-between gap-10 pt-5">
            {{-- Date --}}
            @if ($date)
                @php
                    $dateObject = \Carbon\Carbon::parse($date);
                    $formattedDate = $dateObject->format('F j, Y');
                @endphp

                <time
                    datetime="{{ $date }}"
                    class="shrink-0 text-sm opacity-50"
                >
                    {{ $formattedDate }}
                </time>
            @endif

            {{-- Content --}}
            <p class="line-clamp-3 max-w-72 text-xs leading-relaxed opacity-80">
                {{ $slot }}
            </p>
        </div>

        {{-- Blur decoration --}}
        <div
            class="absolute -top-10 -left-10 -z-50 h-3/4 w-40 rounded-full bg-white opacity-0 blur-3xl transition duration-300 group-hover:opacity-100 dark:bg-white/15"
        ></div>
    </article>
</a>
