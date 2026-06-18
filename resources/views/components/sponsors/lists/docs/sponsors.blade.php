@php
    $sponsors = [
        [
            'url' => 'https://artisan.build/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp',
            'name' => 'Artisan Build',
            'image' => '/img/sponsors/artisan-build.webp',
            'imageDark' => '/img/sponsors/artisan-build-dark.webp',
            'class' => 'w-full',
        ],
    ];

    $sponsor = $sponsors[array_rand($sponsors)];
@endphp

<a
    href="{{ $sponsor['url'] }}"
    title="Learn more about {{ $sponsor['name'] }}"
    aria-label="Visit {{ $sponsor['name'] }} website"
    target="_blank"
    rel="noopener sponsored"
    class="mx-auto flex w-full max-w-40 items-center justify-center px-2 opacity-70 transition duration-200 hover:opacity-100"
>
    @if (isset($sponsor['component']))
        <x-dynamic-component
            :component="$sponsor['component']"
            class="{{ $sponsor['class'] }}"
            aria-hidden="true"
        />
    @else
        <img
            src="{{ $sponsor['image'] }}"
            alt="{{ $sponsor['name'] }} logo"
            class="{{ $sponsor['class'] }} dark:hidden"
            loading="lazy"
        />
        <img
            src="{{ $sponsor['imageDark'] }}"
            alt="{{ $sponsor['name'] }} logo"
            class="{{ $sponsor['class'] }} hidden dark:block"
            loading="lazy"
        />
    @endif
</a>
