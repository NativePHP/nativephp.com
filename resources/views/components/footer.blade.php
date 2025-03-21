<footer
    class="2xl:max-w-8xl mx-auto max-w-5xl px-5 pb-5 pt-20 xl:max-w-7xl"
    aria-label="Site footer"
>
    <div class="flex flex-wrap items-center justify-between gap-6">
        <a
            href="/"
            class="transition duration-200 will-change-transform hover:scale-[1.02]"
            aria-label="Return to homepage"
        >
            <x-logo
                class="h-5"
                aria-hidden="true"
            />
            <span class="sr-only">NativePHP homepage</span>
        </a>
        <div
            class="flex flex-wrap items-center justify-center gap-2.5"
            aria-label="Social networks"
        >
            <x-social-networks-all />
        </div>
    </div>
    <div
        class="flex items-center pb-3 pt-5"
        aria-hidden="true"
    >
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
    </div>
    <div
        class="flex flex-wrap items-center justify-between gap-x-5 gap-y-3 text-sm text-gray-500"
    >
        <div>
            <span>Logo by</span>
            <a
                href="https://twitter.com/caneco"
                target="_blank"
                class="transition duration-200 hover:text-black"
                aria-label="Caneco on Twitter"
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
                class="transition duration-200 hover:text-black"
                aria-label="Marcel Pociot on Twitter"
                rel="noopener noreferrer"
            >
                Marcel Pociot
            </a>
            <span>and</span>
            <a
                href="https://twitter.com/simonhamp"
                target="_blank"
                class="transition duration-200 hover:text-black"
                aria-label="Simon Hamp on Twitter"
                rel="noopener noreferrer"
            >
                Simon Hamp
            </a>
            <span>.</span>
        </div>
    </div>
</footer>
