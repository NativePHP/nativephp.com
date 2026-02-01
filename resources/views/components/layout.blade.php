<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        // Persisted theme preference: 'light' | 'dark' | 'system'
        themePreference: $persist('system').as('themeMode'),
        // Effective dark-mode flag derived from preference + OS
        isDark: false,
        prefersDarkQuery: window.matchMedia('(prefers-color-scheme: dark)'),
        applyTheme() {
            this.isDark =
                this.themePreference === 'dark' ||
                (this.themePreference === 'system' && this.prefersDarkQuery.matches)
        },
        init() {
            const valid = ['light', 'dark', 'system']

            // Initial compute
            this.applyTheme()

            // React to OS preference changes while in 'system' mode
            this.prefersDarkQuery.addEventListener('change', () => {
                if (this.themePreference === 'system') {
                    this.applyTheme()
                }
            })

            // React to user-selected preference changes
            this.$watch('themePreference', () => this.applyTheme())
        },
    }"
    x-bind:class="{ 'dark': isDark === true }"
>
    <head>
        <meta
            http-equiv="Content-Security-Policy"
            content="upgrade-insecure-requests"
        />
        <meta charset="utf-8" />
        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        />
        <meta
            name="csrf-token"
            content="{{ csrf_token() }}"
        />

        @php
            $seoTitle = SEOMeta::getTitle();
            $defaultSeoTitle = config('seotools.meta.defaults.title');
        @endphp

        @if ($seoTitle === $defaultSeoTitle)
            <title>{{ isset($title) ? $title . ' - ' : '' }}NativePHP</title>
        @endif

        {{-- Favicon --}}
        <link
            rel="icon"
            href="{{ asset('favicon.svg') }}"
            type="image/svg+xml"
        />

        {!! SEOMeta::generate() !!}
        {!! OpenGraph::generate() !!}
        {!! Twitter::generate() !!}

        <!-- Fathom - beautiful, simple website analytics -->
        @production
            <script
                src="https://cdn.usefathom.com/script.js"
                data-site="HALHTNZU"
                defer
            ></script>
        @endproduction

        <!-- / Fathom -->

        {{-- Styles --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @livewireStyles
        @vite('resources/css/app.css')
        @stack('head')
    </head>
    <body
        x-cloak
        x-data="{
            showMobileMenu: false,
            showDocsMenu: false,
            scrolled: window.scrollY > 1,
            width: window.innerWidth,
            get showPlatformSwitcherHeader() {
                return ! this.scrolled && this.width >= 1024
            },
        }"
        x-resize="
            width = $width
            if (width >= 1024) {
                showMobileMenu = false
                showDocsMenu = false
            }
        "
        x-init="
            window.addEventListener('scroll', () => {
                scrolled = window.scrollY > 1
            })
        "
        class="font-poppins min-h-screen overflow-x-clip antialiased selection:bg-black selection:text-[#b4a9ff] dark:bg-[#050714] dark:text-white"
    >
        <x-mobile-free-banner />

        <x-navigation-bar />

        <div
            class="mx-auto w-full max-w-5xl px-5 lg:px-3 xl:max-w-7xl 2xl:max-w-360"
        >
            {{ $slot }}
        </div>

        <x-footer />
        @livewireScriptConfig
        @vite('resources/js/app.js')
        @vite('resources/css/docsearch.css')
    </body>
</html>
