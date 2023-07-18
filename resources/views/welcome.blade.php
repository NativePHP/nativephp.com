<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>NativePHP | Baking Delicious Native Apps</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link
            href="https://fonts.bunny.net/css?family=be-vietnam-pro:700|inter:400,500,600|rubik:400,700"
            rel="stylesheet"
        />
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        @vite(["resources/css/app.css", "resources/js/app.js"])
    </head>
    <body class="w-full h-screen text-slate-900 bg-gray-50">
        <main id="app" class="min-h-screen flex items-center flex-col justify-center">
            <header class="md:py-12 flex flex-col items-center justify-center text-center flex-1 gap-12">
                <img src="/logo.svg" class="px-12 lg:h-32">
                <h1 class="sr-only">NativePHP</h1>
                <h3 class="lg:text-2xl md:text-xl px-6 mt-12 text-lg leading-tight">
                    NativePHP is a new way to build native applications,
                    <br class="sm:block hidden">
                    using the tools you already know.
                </h3>
                @if(now()->isAfter('2023-07-20 00:00:00'))
                    <div class="sm:flex-row sm:space-x-6 flex flex-col items-center mt-6">
                        <a href="/docs/" class="sm:w-auto focus:outline-none w-full px-12 py-4 text-lg font-bold text-gray-900 bg-white border rounded-lg">
                            Get started
                        </a>
                        <a href="https://github.com/nativephp/laravel" target="_blank" class="sm:w-auto focus:outline-none sm:mt-0 w-full px-12 py-4 mt-3 text-lg font-bold text-gray-900 bg-transparent border rounded-lg">
                            Source code
                        </a>
                    </div>
                @else
                    <div class="underline text-lg">
                        Coming July 20, at <a href="https://laracon.us">Laracon US</a>.
                    </div>
                @endif
            </header>

            <footer class="md:px-0 p-12 justify-end">
                <small class="md:text-xs block text-sm text-center">© {{ date('Y') }} NativePHP</small>
            </footer>

        </main>

    </body>
</html>
