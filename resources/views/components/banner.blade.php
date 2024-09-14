<div class="lg:sticky top-0 blur-background text-white bg-gray-50/85 border-b border-gray-100 z-50 dark:bg-gray-800/85 dark:border-0">

{{--    <x-alert />--}}
    <div class="flex items-center justify-between gap-6 px-6 py-3 mx-auto max-w-screen-xl sm:py-4">
        <a
            href="/"
            class="inline-flex items-center gap-3 transition rounded hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-red-600"
        >
            <img src="{{ asset('logo.svg') }}" class="h-8 dark:hidden">
            <img src="{{ asset('logo-dark.svg') }}" class="hidden h-8 dark:block">
            <span class="sr-only">NativePHP</span>
        </a>

        <div class="flex items-center space-x-6">
            <div id="docsearch" class="mr-12"></div>

            <a href="https://discord.gg/X62tWNStZK" title="Go to discord server">
                <x-icons.discord  class="size-5 dark:fill-white hover:fill-[#00aaa6]" />
            </a>

            <a href="https://pinkary.com/@nativephp" title="Pinkary of NativePHP">
                <x-icons.pinkary class="size-5 text-black dark:invert hover:text-[#00aaa6] hover:invert-0" />
            </a>

            <a href="https://github.com/nativephp" title="Source code of NativePHP">
                <x-icons.github class="size-5  dark:fill-white hover:fill-[#00aaa6]" />
            </a>
        </div>
    </div>
</div>
