@php
    $partners = [
        [
            'url' => 'https://nexcalia.com/?ref=nativephp',
            'name' => 'Nexcalia',
            'tagline' => 'Smart tools for scheduling & visitor management',
            'component' => 'sponsors.logos.nexcalia',
            'class' => 'w-full text-black dark:text-white',
        ],
        [
            'url' => 'https://www.webmavens.com/?ref=nativephp',
            'name' => 'Web Mavens',
            'tagline' => 'Build Secure, Scalable Web Apps',
            'component' => 'sponsors.logos.webmavens',
            'class' => 'w-full dark:fill-white',
        ],
        [
            'url' => 'https://synergitech.co.uk/partners/nativephp/',
            'name' => 'Synergi Tech',
            'tagline' => 'Bespoke software for complex infrastructure',
            'image' => '/img/sponsors/synergi.svg',
            'imageDark' => '/img/sponsors/synergi-dark.svg',
            'class' => 'w-full',
        ],
        [
            'url' => 'https://laradevs.com/?ref=nativephp',
            'name' => 'Laradevs',
            'tagline' => 'Hire the best Laravel developers anywhere',
            'component' => 'sponsors.logos.laradevs',
            'class' => 'w-full text-black dark:text-white',
        ],
        [
            'url' => 'https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp',
            'name' => 'BeyondCode',
            'tagline' => 'Essential tools for web developers',
            'image' => '/img/sponsors/beyondcode.webp',
            'imageDark' => '/img/sponsors/beyondcode-dark.webp',
            'class' => 'w-full',
        ],
    ];

    $partner = $partners[array_rand($partners)];
@endphp

<a
    href="{{ $partner['url'] }}"
    title="Learn more about {{ $partner['name'] }}"
    aria-label="Visit {{ $partner['name'] }} website"
    class="mx-auto flex w-full max-w-64 shrink-0 flex-col items-center gap-2 rounded-2xl bg-gray-100 px-5 py-4 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
    rel="noopener sponsored"
>
    <div class="flex w-full items-center justify-center px-2">
        @if (isset($partner['component']))
            <x-dynamic-component
                :component="$partner['component']"
                class="{{ $partner['class'] }}"
                aria-hidden="true"
            />
        @else
            <img
                src="{{ $partner['image'] }}"
                alt="{{ $partner['name'] }} logo"
                class="{{ $partner['class'] }} dark:hidden"
                loading="lazy"
            />
            <img
                src="{{ $partner['imageDark'] }}"
                alt="{{ $partner['name'] }} logo"
                class="{{ $partner['class'] }} hidden dark:block"
                loading="lazy"
            />
        @endif
    </div>
    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $partner['tagline'] }}</span>
</a>
