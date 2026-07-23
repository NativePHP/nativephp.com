<a
    href="/docs/mobile/4/architecture/super-native"
    onclick="fathom.trackEvent('supernative_banner_click')"
    data-site-banner
    class="group relative z-30 flex flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-cyan-100 via-sky-50 to-cyan-100 px-5 py-3 select-none 3xs:flex-row dark:from-cyan-950/50 dark:via-sky-950/50 dark:to-cyan-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.sparkles
            class="size-5 shrink-0 text-cyan-600 dark:text-cyan-400"
            aria-hidden="true"
        />

        {{-- Text --}}
        <div>
            <style>
                .supernative-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-cyan-600) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: supernative-shine 2s linear infinite;
                }
                .dark .supernative-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-cyan-400) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes supernative-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="supernative-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                <b>NativePHP for Mobile v4</b>
                is here &mdash; build real native UIs from Blade with
                <b>SuperNative</b>
            </div>
        </div>
    </div>

    {{-- Arrow --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>
