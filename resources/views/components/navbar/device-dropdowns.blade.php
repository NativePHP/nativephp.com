<nav class="flex items-center gap-2.5">
    {{-- Mobile dropdown --}}
    <x-navbar.device-dropdown
        label="Mobile"
        icon="tablet-smartphone"
        id="mobile-dropdown"
    >
        <x-navbar.device-dropdown-item
            x-ref="firstItem"
            href="/docs/mobile/1/getting-started/introduction"
            aria-label="NativePHP for Mobile"
            title="Documentation"
            subtitle="Get started with Mobile"
            tooltip="Get started with Mobile"
            icon="docs"
            icon-class="size-4.5"
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
            href="https://github.com/nativephp"
            title="GitHub"
            subtitle="Visit our GitHub repository"
            icon="github"
            x-ref="firstItem"
        />
        <x-navbar.device-dropdown-item
            href="/sponsor"
            aria-label="Sponsor NativePHP"
            title="Sponsor"
            subtitle="Support NativePHP development"
            tooltip="Support NativePHP development"
            icon="heart"
            icon-class="size-4"
        />
    </x-navbar.device-dropdown>
</nav>
