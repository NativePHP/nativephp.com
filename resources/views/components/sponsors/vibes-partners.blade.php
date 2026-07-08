@php
    $partners = [
        [
            'url' => 'https://www.webmavens.com/?ref=nativephp',
            'name' => 'Web Mavens',
            'component' => 'sponsors.logos.webmavens',
            'class' => 'dark:fill-white',
        ],
        [
            'url' => 'https://www.nexcalia.com/?ref=nativephp',
            'name' => 'Nexcalia',
            'component' => 'sponsors.logos.nexcalia',
            'class' => 'text-black dark:text-white',
        ],
        [
            'url' => 'https://bifrost.nativephp.com/',
            'name' => 'Bifrost Technology',
            'component' => 'logos.bifrost',
            'class' => 'h-6',
        ],
        [
            'url' => 'https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=the-vibes',
            'name' => 'Beyond Code',
            'image' => '/img/sponsors/beyondcode.webp',
            'imageDark' => '/img/sponsors/beyondcode-dark.webp',
            'width' => 160,
            'height' => 40,
        ],
        [
            'url' => 'https://mostlytechnical.com/',
            'name' => 'Mostly Technical',
            'image' => '/img/sponsors/mostly-technical.webp',
            'class' => 'h-15 w-15 rounded',
            'width' => 60,
            'height' => 60,
        ],
        [
            'url' => 'https://www.geocod.io/?utm_source=nativephp&utm_medium=sponsorship&utm_campaign=thevibes',
            'name' => 'Geocodio',
            'component' => 'sponsors.logos.geocodio',
        ],
        [
            'url' => 'https://thephp.foundation/?utm_source=nativephp&utm_medium=sponsorship&utm_campaign=thevibes',
            'name' => 'The PHP Foundation',
            'component' => 'sponsors.logos.php-foundation',
            'class' => 'h-15 w-15 rounded',
        ],
        [
            'url' => 'https://artisan.build/?utm_source=nativephp&utm_medium=sponsorship&utm_campaign=thevibes',
            'name' => 'Artisan Build',
            'image' => '/img/sponsors/artisan-build.webp',
            'imageDark' => '/img/sponsors/artisan-build-dark.webp',
            'width' => 160,
            'height' => 20,
        ],
        [
            'url' => 'https://nopticon.com/?ref=nativephp',
            'name' => 'Nopticon',
            'component' => 'sponsors.logos.nopticon',
            'class' => 'text-black dark:text-white',
        ],
        [
            'url' => 'https://jump24.co.uk/?ref=nativephp',
            'name' => 'Jump24',
            'component' => 'sponsors.logos.jump24',
            'class' => 'h-10 text-black dark:text-white',
        ],
    ];
@endphp

@foreach ($partners as $partner)
    <a
        href="{{ $partner['url'] }}"
        target="_blank"
        rel="noopener noreferrer sponsored"
        class="grid h-28 place-items-center rounded-2xl bg-gray-100 px-6 transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-gray-200/80 hover:shadow-lg hover:shadow-gray-200/70 dark:bg-[#1a1a2e] dark:hover:bg-slate-800/80 dark:hover:shadow-transparent"
    >
        <div class="grid h-15 w-35 place-items-center">
            @if (isset($partner['component']))
                <x-dynamic-component
                    :component="$partner['component']"
                    :class="$partner['class'] ?? ''"
                    aria-hidden="true"
                />
            @elseif (isset($partner['imageDark']))
                <img
                    src="{{ $partner['image'] }}"
                    class="block dark:hidden"
                    loading="lazy"
                    alt="{{ $partner['name'] }} logo"
                    width="{{ $partner['width'] }}"
                    height="{{ $partner['height'] }}"
                />
                <img
                    src="{{ $partner['imageDark'] }}"
                    class="hidden dark:block"
                    loading="lazy"
                    alt="{{ $partner['name'] }} logo"
                    width="{{ $partner['width'] }}"
                    height="{{ $partner['height'] }}"
                />
            @else
                <img
                    src="{{ $partner['image'] }}"
                    loading="lazy"
                    alt="{{ $partner['name'] }} logo"
                    width="{{ $partner['width'] }}"
                    height="{{ $partner['height'] }}"
                    class="{{ $partner['class'] ?? '' }}"
                />
            @endif
        </div>
        <span class="sr-only">{{ $partner['name'] }}</span>
    </a>
@endforeach
