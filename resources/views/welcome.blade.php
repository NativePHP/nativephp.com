<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>NativePHP | Baking Delicious Native Apps</title>
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link
        href="https://fonts.bunny.net/css?family=be-vietnam-pro:700|inter:400,500,600|rubik:400,700"
        rel="stylesheet"
    />
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    @vite(["resources/css/app.css", "resources/js/app.js"])

    <!-- Fathom - beautiful, simple website analytics -->
    <script src="https://cdn.usefathom.com/script.js" data-site="HALHTNZU" defer></script>
    <!-- / Fathom -->
</head>
<body class="background-pattern w-full h-screen text-slate-900 bg-gray-50 dark:bg-gray-900 dark:text-white">
<x-alert/>

<main id="app" class="flex flex-col items-center min-h-screen">
    <header class="flex flex-col items-center flex-1 gap-12 text-center md:py-12">
        <img src="{{ asset('logo.svg') }}" class="px-12 lg:h-32 dark:hidden mt-24">
        <img src="{{ asset('logo-dark.svg') }}" class="hidden px-12 lg:h-32 dark:block mt-24">
        <h1 class="sr-only">NativePHP</h1>
        <h3 class="px-6 mt-12 text-lg leading-tight lg:text-2xl md:text-xl">
            NativePHP is a new way to build native applications,
            <br class="hidden sm:block">
            using the tools you already know.
        </h3>
        <div class="flex flex-col items-center mt-6 sm:flex-row sm:space-x-6">
            <a href="/docs/" class="w-full px-12 py-4 text-lg font-bold text-gray-900 bg-white border rounded-lg sm:w-auto focus:outline-none">
                Get started
            </a>
            <a href="https://github.com/nativephp/laravel" target="_blank" class="w-full px-12 py-4 mt-3 text-lg font-bold text-gray-900 border rounded-lg dark:text-white sm:w-auto focus:outline-none sm:mt-0 bg-gray-50 dark:bg-gray-900">
                Source code
            </a>
        </div>

        <div class="mt-6 px-12">
            <h2 class="text-2xl font-bold">Featured Sponsors</h2>

            <div class="flex flex-col sm:flex-row gap-16 items-center justify-center py-8">
                <x-sponsors-featured/>
            </div>

            <h2 class="text-2xl font-bold py-8">Corporate Sponsors</h2>

            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-x-16 gap-y-8 items-center justify-center">
                <x-sponsors-corporate/>
            </div>

            <a href="/docs/getting-started/sponsoring" class="inline-block px-4 py-1.5 text-xs font-semibold text-black bg-white border rounded mt-6">
                Want your logo here?
            </a>
        </div>
    </header>

    <x-footer/>
</main>

</body>
</html>
