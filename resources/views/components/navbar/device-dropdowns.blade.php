@php
    $showShowcase = \App\Models\Showcase::approved()->count() >= 4;
    $mobileAirStars = \App\Support\GitHub::mobileAir()->formattedStars();
    $desktopStars = \App\Support\GitHub::desktop()->formattedStars();
@endphp

<nav class="flex items-center gap-1.5 lg:gap-2">
    {{-- Mobile dropdown --}}
    <x-navbar.device-dropdown
        label="Mobile"
        icon="device-mobile-phone"
        id="mobile-dropdown"
    >
        <x-navbar.device-dropdown-item
            href="/docs/mobile/getting-started/introduction"
            title="Documentation"
            subtitle="Get started with Mobile"
            icon="docs"
        />
        @feature(App\Features\ShowPlugins::class)
            <x-navbar.device-dropdown-item
                href="{{ route('plugins') }}"
                title="Plugins"
                subtitle="Extend your mobile apps"
                icon="plug"
                icon-class="size-5"
            />
        @endfeature
        @if($showShowcase)
            <x-navbar.device-dropdown-item
                href="{{ route('showcase', 'mobile') }}"
                title="Showcase"
                subtitle="Apps built with NativePHP"
                icon="star"
                icon-class="size-4"
            />
        @endif
        <x-navbar.device-dropdown-item
            href="https://github.com/nativephp/mobile-air"
            title-prefix="nativephp/"
            title="mobile-air"
            :subtitle="$mobileAirStars ?? 'GitHub'"
            icon="github"
        />
    </x-navbar.device-dropdown>

    {{-- Desktop dropdown --}}
    <x-navbar.device-dropdown
        label="Desktop"
        icon="pc"
        id="desktop-dropdown"
        align="center"
    >
        <x-navbar.device-dropdown-item
            href="/docs/desktop/getting-started/introduction"
            title="Documentation"
            subtitle="Get started with Desktop"
            icon="docs"
        />
        @if($showShowcase)
            <x-navbar.device-dropdown-item
                href="{{ route('showcase', 'desktop') }}"
                title="Showcase"
                subtitle="Apps built with NativePHP"
                icon="star"
                icon-class="size-4"
            />
        @endif
        <x-navbar.device-dropdown-item
            href="https://github.com/nativephp/desktop"
            title-prefix="nativephp/"
            title="desktop"
            :subtitle="$desktopStars ?? 'GitHub'"
            icon="github"
        />
    </x-navbar.device-dropdown>
</nav>
