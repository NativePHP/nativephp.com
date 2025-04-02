<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode: $persist(
            window.matchMedia('(prefers-color-scheme: dark)').matches,
        ),
    }"
    x-bind:class="{ 'dark': darkMode === true }"
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

        <title>NativePHP{{ isset($title) ? ' | ' . $title : '' }}</title>

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
        <script
            src="https://cdn.usefathom.com/script.js"
            data-site="HALHTNZU"
            defer
        ></script>
        <!-- / Fathom -->

        {{-- Styles --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @vite('resources/css/app.css')
    </head>
    <body
        x-cloak
        x-data="{
            showDocsNavigation: false,
            scrolled: window.scrollY > 50,
            width: window.innerWidth,
            get showPlatformSwitcherHeader() {
                return ! this.scrolled && this.width >= 1024
            },
        }"
        x-resize="
            width = $width
            if (width >= 1024) showDocsNavigation = false
        "
        x-init="
            window.addEventListener('scroll', () => {
                scrolled = window.scrollY > 50
            })
        "
        x-effect="
            if (showDocsNavigation) {
                document.body.style.overflow = 'hidden'
            } else {
                document.body.style.overflow = ''
            }
        "
        class="min-h-screen overflow-x-clip font-poppins antialiased selection:bg-black selection:text-[#b4a9ff] dark:bg-[#050714] dark:text-white"
    >
        <x-navigation-bar :hasMenu="$hasMenu ?? false" />
        {{ $slot }}
        <x-footer />
        @vite('resources/js/app.js')
        @vite('resources/css/docsearch.css')
    </body>
</html>
