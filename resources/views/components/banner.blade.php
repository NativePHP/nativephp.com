<div class="relative text-white bg-gray-50 dark:bg-gray-800">
    <div
        class="flex items-center justify-between gap-6 px-6 py-3 mx-auto max-w-7xl sm:py-4"
    >
        <a
            href="/"
            class="inline-flex items-center gap-3 transition rounded hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-red-600"
        >
            <img src="{{ asset('logo.svg') }}" class="h-8 dark:hidden">
            <img src="{{ asset('logo-dark.svg') }}" class="hidden h-8 dark:block">
            <span class="sr-only">NativePHP</span>
        </a>

        <div class="flex items-center space-x-12">
            <div id="docsearch"></div>

            <a href="https://discord.gg/X62tWNStZK">
                <x-icons.discord class="w-5 h-5 dark:fill-white" />
            </a>

            <a href="https://github.com/nativephp">
                <x-icons.github class="w-5 h-5 dark:fill-white" />
            </a>
        </div>
    </div>
</div>
