<nav class="flex items-center gap-2">
    {{-- Mobile dropdown --}}
    <x-navbar.device-dropdown
        label="Mobile"
        icon="device-mobile-phone"
        id="mobile-dropdown"
    >
        <x-navbar.device-dropdown-item
            href="/docs/mobile/1/getting-started/introduction"
            title="Documentation"
            subtitle="Get started with Mobile"
            icon="docs"
        />
        <x-navbar.device-dropdown-item
            href="{{ route('early-adopter') }}"
            title="Pricing"
            subtitle="See our pricing plans"
            icon="dollar-circle"
            icon-class="size-5.5"
        />
        <x-navbar.device-dropdown-item
            href="https://github.com/nativephp"
            title="GitHub"
            subtitle="Visit our GitHub repository"
            icon="github"
        />
    </x-navbar.device-dropdown>

    {{-- Desktop dropdown --}}
    <x-navbar.device-dropdown
        label="Desktop"
        icon="pc"
        id="desktop-dropdown"
    >
        <x-navbar.device-dropdown-item
            href="/docs/desktop/1/getting-started/introduction"
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
        <x-navbar.device-dropdown-item
            href="https://github.com/nativephp"
            title="GitHub"
            subtitle="Visit our GitHub repository"
            icon="github"
        />
    </x-navbar.device-dropdown>
</nav>
