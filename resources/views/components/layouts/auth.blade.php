<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        themePreference: $persist('system').as('themeMode'),
        isDark: false,
        prefersDarkQuery: window.matchMedia('(prefers-color-scheme: dark)'),
        applyTheme() {
            this.isDark =
                this.themePreference === 'dark' ||
                (this.themePreference === 'system' && this.prefersDarkQuery.matches)
        },
        init() {
            this.applyTheme()
            this.prefersDarkQuery.addEventListener('change', () => {
                if (this.themePreference === 'system') {
                    this.applyTheme()
                }
            })
            this.$watch('themePreference', () => this.applyTheme())
        },
    }"
    x-bind:class="{ 'dark': isDark === true }"
>
    <head>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ isset($title) ? $title . ' - ' : '' }}NativePHP</title>

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />

        <style>
            [x-cloak] { display: none !important; }
        </style>
        @livewireStyles
        @vite('resources/css/app.css')
    </head>
    <body
        x-cloak
        class="flex min-h-screen items-center justify-center bg-white font-poppins antialiased dark:bg-zinc-900 dark:text-white"
    >
        <div class="w-full max-w-sm px-4 py-12">
            <div class="mb-8 text-center">
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset('favicon.svg') }}" alt="NativePHP" class="mx-auto h-10 w-10" />
                </a>
            </div>

            {{ $slot }}
        </div>

        @livewireScriptConfig
        @fluxScripts
        @vite('resources/js/app.js')
    </body>
</html>
