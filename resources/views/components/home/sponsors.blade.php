@php
    $sponsors = [
        [
            'url' => 'https://artisan.build/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp',
            'name' => 'Artisan Build',
            'image' => '/img/sponsors/artisan-build.webp',
            'imageDark' => '/img/sponsors/artisan-build-dark.webp',
        ],
        [
            'url' => 'https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp',
            'name' => 'BeyondCode',
            'image' => '/img/sponsors/beyondcode.webp',
            'imageDark' => '/img/sponsors/beyondcode-dark.webp',
        ],
        [
            'url' => 'https://laradevs.com/?ref=nativephp',
            'name' => 'Laradevs',
            'component' => 'sponsors.logos.laradevs',
            'class' => 'h-6 w-auto text-black dark:text-white',
        ],
    ];
@endphp

<section
    class="my-16 lg:my-24"
    aria-labelledby="home-sponsors-title"
    role="region"
>
    <div
        x-init="
            () => {
                motion.inView($el, (element) => {
                    gsap.fromTo(
                        $el,
                        { y: 10, autoAlpha: 0 },
                        {
                            y: 0,
                            autoAlpha: 1,
                            duration: 0.7,
                            ease: 'power2.out',
                        },
                    )
                })
            }
        "
        class="flex flex-col items-center gap-6"
    >
        <h2
            id="home-sponsors-title"
            class="text-sm font-medium tracking-wide text-gray-500 uppercase dark:text-zinc-500"
        >
            Sponsored by
        </h2>

        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-5">
            @foreach ($sponsors as $sponsor)
                <a
                    wire:key="home-sponsor-{{ \Illuminate\Support\Str::slug($sponsor['name']) }}"
                    href="{{ $sponsor['url'] }}"
                    title="Learn more about {{ $sponsor['name'] }}"
                    aria-label="Visit {{ $sponsor['name'] }} website"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="opacity-70 transition duration-200 hover:opacity-100"
                >
                    @if (isset($sponsor['component']))
                        <x-dynamic-component
                            :component="$sponsor['component']"
                            :class="$sponsor['class'] ?? ''"
                            aria-hidden="true"
                        />
                    @else
                        <img
                            src="{{ $sponsor['image'] }}"
                            class="block h-6 w-auto dark:hidden"
                            loading="lazy"
                            alt="{{ $sponsor['name'] }} logo"
                        />
                        <img
                            src="{{ $sponsor['imageDark'] }}"
                            class="hidden h-6 w-auto dark:block"
                            loading="lazy"
                            alt="{{ $sponsor['name'] }} logo"
                        />
                    @endif
                </a>
            @endforeach

            <a
                href="{{ route('sponsoring') }}"
                class="text-sm text-gray-500 underline decoration-gray-300 underline-offset-4 transition duration-200 hover:text-gray-700 dark:text-zinc-500 dark:decoration-zinc-700 dark:hover:text-zinc-300"
            >
                Become a sponsor
            </a>
        </div>
    </div>
</section>
