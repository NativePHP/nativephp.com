<x-layout title="Baking Delicious Native Apps">
    <main
        id="app"
        class="flex min-h-screen flex-col items-center"
    >
        <header
            class="flex flex-1 flex-col items-center gap-12 text-center md:py-12"
        >
            <img
                src="{{ asset('logo.svg') }}"
                class="mt-24 px-12 lg:h-32 dark:hidden"
            />
            <img
                src="{{ asset('logo-dark.svg') }}"
                class="mt-24 hidden px-12 lg:h-32 dark:block"
            />
            <h1 class="sr-only">NativePHP</h1>
            <h3 class="mt-12 px-6 text-lg leading-tight md:text-xl lg:text-2xl">
                NativePHP is a new way to build native applications,
                <br class="hidden sm:block" />
                using the tools you already know.
            </h3>
            <div
                class="mt-6 flex flex-col items-center sm:flex-row sm:space-x-6"
            >
                <a
                    href="/docs/"
                    class="w-full rounded-lg border bg-white px-12 py-4 text-lg font-bold text-gray-900 focus:outline-none sm:w-auto"
                >
                    Get started
                </a>
                <a
                    href="https://github.com/nativephp/laravel"
                    target="_blank"
                    class="mt-3 w-full rounded-lg border bg-gray-50 px-12 py-4 text-lg font-bold text-gray-900 focus:outline-none sm:mt-0 sm:w-auto dark:bg-gray-900 dark:text-white"
                >
                    Source code
                </a>
            </div>

            <div class="mt-6 px-12">
                <h2 class="text-2xl font-bold">Featured Sponsors</h2>

                <div
                    class="flex flex-col items-center justify-center gap-16 py-8 sm:flex-row"
                >
                    <x-sponsors-featured />
                </div>

                <h2 class="py-8 text-2xl font-bold">Corporate Sponsors</h2>

                <div
                    class="flex flex-col items-center justify-center gap-x-16 gap-y-8 sm:flex-row sm:flex-wrap"
                >
                    <x-sponsors-corporate />
                </div>

                <a
                    href="/docs/getting-started/sponsoring"
                    class="mt-6 inline-block rounded border bg-white px-4 py-1.5 text-xs font-semibold text-black"
                >
                    Want your logo here?
                </a>
            </div>
        </header>

        <x-footer />
    </main>
</x-layout>
