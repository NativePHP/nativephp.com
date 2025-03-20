<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/@docsearch/css@3"
        />

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
        x-data="{ showDocsNavigation: false, scrolled: false }"
        x-init="
            window.addEventListener('scroll', () => {
                scrolled = window.scrollY > 20
            })
        "
        class="min-h-screen overflow-x-clip font-poppins antialiased selection:bg-black selection:text-[#b4a9ff] dark:bg-[#050714] dark:text-white"
    >
        <x-navigation-bar :hasMenu="$hasMenu ?? false" />
        {{ $slot }}
        <x-footer />
        <script src="https://cdn.jsdelivr.net/npm/@docsearch/js@3"></script>
        <script type="text/javascript">
            docsearch({
                appId: 'ZNII9QZ8WI',
                apiKey: '9be495a1aaf367b47c873d30a8e7ccf5',
                indexName: 'nativephp',
                insights: true,
                container: '#docsearch',
                debug: false,
            })
        </script>
        @vite('resources/js/app.js')
    </body>
</html>
