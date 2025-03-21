<footer
    class="2xl:max-w-8xl mx-auto max-w-5xl px-5 pb-5 pt-20 xl:max-w-7xl"
    aria-labelledby="footer-heading"
>
    <div class="flex flex-wrap items-center justify-between gap-6">
        {{-- Logo --}}
        <a
            href="/"
            class="transition duration-200 will-change-transform hover:scale-[1.02]"
            aria-label="NativePHP homepage"
        >
            <x-logo
                class="h-5"
                aria-hidden="true"
                alt="NativePHP Logo"
            />
            <span class="sr-only">NativePHP homepage</span>
        </a>

        {{-- Social links --}}
        <nav
            class="flex flex-wrap items-center justify-center gap-2.5"
            aria-label="Social networks"
        >
            <x-social-networks-all />
        </nav>
    </div>

    {{-- Divider --}}
    <div
        class="flex items-center pb-3 pt-5"
        aria-hidden="true"
    >
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
    </div>

    {{-- Copyright --}}
    <section
        class="flex flex-wrap items-center justify-between gap-x-5 gap-y-3 text-sm text-gray-500 dark:text-gray-400/80"
        aria-label="Credits and copyright information"
    >
        <div>
            <span>Logo by</span>
            <a
                href="https://twitter.com/caneco"
                target="_blank"
                class="transition duration-200 hover:text-black dark:hover:text-white"
                aria-label="Caneco's Twitter profile"
                rel="noopener noreferrer"
            >
                Caneco
            </a>
            <span>.</span>
        </div>
        <div>
            <span>© {{ date('Y') }} Maintained by</span>
            <a
                href="https://twitter.com/marcelpociot"
                target="_blank"
                class="transition duration-200 hover:text-black dark:hover:text-white"
                aria-label="Marcel Pociot's Twitter profile"
                rel="noopener noreferrer"
            >
                Marcel Pociot
            </a>
            <span>and</span>
            <a
                href="https://twitter.com/simonhamp"
                target="_blank"
                class="transition duration-200 hover:text-black dark:hover:text-white"
                aria-label="Simon Hamp's Twitter profile"
                rel="noopener noreferrer"
            >
                Simon Hamp
            </a>
            <span>.</span>
        </div>
    </section>
</footer>
