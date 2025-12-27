@php
    $showShowcase = \App\Models\Showcase::approved()->count() >= 4;
@endphp

<nav class="flex items-center gap-1.5 lg:gap-2">
    {{-- Mobile dropdown --}}
    <x-navbar.device-dropdown
        label="Mobile"
        icon="device-mobile-phone"
        id="mobile-dropdown"
    >
        <x-navbar.device-dropdown-item
            href="/docs/mobile/3/getting-started/introduction"
            title="Documentation"
            subtitle="Get started with Mobile"
            icon="docs"
        />
        <x-navbar.device-dropdown-item
            href="{{ route('pricing') }}"
            title="Pricing"
            subtitle="See our pricing plans"
            icon="dollar-circle"
            icon-class="size-5.5"
        />
        @if($showShowcase)
            <x-navbar.device-dropdown-item
                href="{{ route('showcase', 'mobile') }}"
                title="Showcase"
                subtitle="Apps built with NativePHP"
                icon="star"
                icon-class="size-4"
            />
        @endif
    </x-navbar.device-dropdown>

    {{-- Desktop dropdown --}}
    <x-navbar.device-dropdown
        label="Desktop"
        icon="pc"
        id="desktop-dropdown"
        align="center"
    >
        <x-navbar.device-dropdown-item
            href="/docs/desktop/2/getting-started/introduction"
            title="Documentation"
            subtitle="Get started with Desktop"
            icon="docs"
        />
        <x-navbar.device-dropdown-item
            href="/sponsor"
            title="Sponsor"
            subtitle="Support our contributors"
            icon="heart"
            icon-class="size-4"
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
            title="GitHub"
            subtitle="Visit our GitHub repository"
            icon="github"
        />
    </x-navbar.device-dropdown>
</nav>
