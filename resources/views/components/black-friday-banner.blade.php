<a
    href="https://bifrost.nativephp.com/newsletter"
    target="_blank"
    rel="noopener"
    onclick="window.fathom?.trackEvent('bifrost_black_friday_banner_click')"
    data-site-banner
    class="group relative z-30 flex flex-col flex-wrap items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-tl from-orange-100 to-gray-200/60 px-5 py-3 select-none 3xs:flex-row dark:from-orange-950/65 dark:to-gray-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.banner.dollar-decrease
            class="h-5 shrink-0 dark:text-orange-200"
            aria-hidden="true"
        />

        {{-- Text --}}
        <div>
            <style>
                .bf-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-orange-500) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: bf-shine 2s linear infinite;
                }
                .dark .bf-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-orange-300) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes bf-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="bf-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                <b>Black Friday:</b>
                up to
                <b>50% off</b>
                Bifrost annual plans
            </div>
        </div>
    </div>

    {{-- Call to action --}}
    <span
        class="shrink-0 rounded-md bg-white/70 px-2 py-0.5 text-sm font-semibold text-orange-900 ring-1 ring-orange-200 dark:bg-white/10 dark:text-orange-100 dark:ring-white/10"
    >
        Get the code by email
    </span>

    {{-- Arrow --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>
