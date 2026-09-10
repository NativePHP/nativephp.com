<a
    href="https://bifrost.nativephp.com/pricing?billing=yearly"
    onclick="window.fathom?.trackEvent('bifrost_birthday_banner_click')"
    data-site-banner
    class="group relative z-30 flex flex-col flex-wrap items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-pink-100 via-sky-50 to-indigo-100 px-5 py-3 select-none 3xs:flex-row dark:from-pink-950/50 dark:via-sky-950/50 dark:to-indigo-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.party-popper
            class="size-5 shrink-0 text-pink-600 dark:text-pink-400"
            aria-hidden="true"
        />

        {{-- Text --}}
        <div>
            <style>
                .bifrost-birthday-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-pink-600) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: bifrost-birthday-shine 2s linear infinite;
                }
                .dark .bifrost-birthday-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-pink-400) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes bifrost-birthday-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="bifrost-birthday-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                <b>Bifrost turns 1!</b>
                Get
                <b>30% off</b>
                annual plans with code
            </div>
        </div>
    </div>

    {{-- Discount code --}}
    <span
        class="shrink-0 rounded-md bg-white/70 px-2 py-0.5 font-mono text-sm font-bold tracking-wide text-pink-900 ring-1 ring-pink-200 dark:bg-white/10 dark:text-pink-100 dark:ring-white/10"
    >
        HAPPYBIRTHDAY
    </span>

    {{-- Arrow --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>
