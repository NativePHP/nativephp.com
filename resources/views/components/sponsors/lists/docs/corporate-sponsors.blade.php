@php
    $sponsors = [
        [
            'url' => 'https://sevalla.com/?utm_source=nativephp&utm_medium=Referral&utm_campaign=homepage',
            'title' => 'Learn more about Sevalla',
            'label' => 'Visit Sevalla website',
            'component' => 'sponsors.logos.sevalla',
            'class' => 'h-auto max-h-8 max-w-full text-black dark:text-white',
            'alt' => 'Sevalla logo',
            'name' => 'Sevalla',
        ],
        [
            'url' => 'https://www.kaashosting.nl/?lang=en',
            'title' => 'Learn more about KaasHosting',
            'label' => 'Visit KaasHosting website',
            'component' => 'sponsors.logos.kaashosting',
            'class' => 'block h-auto max-h-8 max-w-full fill-[#042340] dark:fill-white',
            'alt' => 'KaasHosting logo',
            'name' => 'KaasHosting',
        ],
        [
            'url' => 'https://www.quantumweb.co/',
            'title' => 'Learn more about Quantumweb',
            'label' => 'Visit Quantumweb website',
            'component' => 'sponsors.logos.quantumweb',
            'class' => 'block h-auto max-h-8 max-w-full fill-[#042340] dark:fill-white',
            'alt' => 'Quantumweb logo',
            'name' => 'Quantumweb',
        ],
        // Uncomment these if you want to include the commented sponsors
        /*
                                    [
                                        'url' => 'https://serverauth.com/',
                                        'title' => 'Learn more about ServerAuth',
                                        'label' => 'Visit ServerAuth website',
                                        'component' => 'sponsors.logos.serverauth',
                                        'class' => 'block h-auto max-h-8 max-w-full fill-[#042340] dark:fill-white',
                                        'alt' => 'ServerAuth logo',
                                        'name' => 'ServerAuth'
                                    ],
                                    [
                                        'url' => 'https://borah.digital/',
                                        'title' => 'Learn more about Borah Digital Labs',
                                        'label' => 'Visit Borah Digital Labs website',
                                        'component' => 'sponsors.logos.borah',
                                        'class' => 'block h-auto max-h-8 max-w-full dark:brightness-125',
                                        'alt' => 'Borah Digital Labs logo',
                                        'name' => 'Borah Digital Labs'
                                    ],
                                */
    ];

    $randomSponsor = $sponsors[array_rand($sponsors)];
@endphp

<a
    href="{{ $randomSponsor['url'] }}"
    class="inline-grid h-16 w-full shrink-0 place-items-center rounded-2xl bg-gray-100 px-5 transition duration-200 will-change-transform hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
    title="{{ $randomSponsor['title'] }}"
    aria-label="{{ $randomSponsor['label'] }}"
    rel="noopener"
>
    <x-dynamic-component
        :component="$randomSponsor['component']"
        class="{{ $randomSponsor['class'] }}"
        alt="{{ $randomSponsor['alt'] }}"
    />
    <span class="sr-only">{{ $randomSponsor['name'] }}</span>
</a>
