<a
    href="/bifrost"
    onclick="fathom.trackEvent('black_friday_banner_click');"
    class="group relative z-30 flex flex-col items-center justify-center gap-3 overflow-hidden bg-gradient-to-tl from-orange-100 to-gray-200/60 px-5 py-3 select-none sm:flex-row dark:from-orange-950/65 dark:to-gray-950/50"
>
    {{-- Label Section --}}
    <div
        class="flex flex-col items-center gap-2 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5 sm:flex-row sm:gap-3"
    >
        {{-- Icon --}}
        <x-icons.banner.dollar-decrease
            class="h-5 shrink-0 dark:text-orange-200"
        />

        {{-- Text --}}
        <div class="text-center sm:text-left">
            <style>
                .bf-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-orange-500) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: shine 2s linear infinite;
                }
                .dark .bf-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-orange-300) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="bf-gradient-text bg-clip-text tracking-tight text-pretty text-transparent"
            >
                <b>Black Friday:</b> <em>40% off</em> Bifrost <b>Hela</b> & <b>Thor</b> plans • Code: <b>BLACKFRIDAY40</b>
            </div>
        </div>
    </div>

    {{-- Countdown Timer --}}
    <div
        x-data="countdown('2025-12-02T00:00:00Z')"
        class="flex items-center gap-2 text-xs font-medium"
    >
        <span class="text-gray-600 dark:text-gray-400">Ends in:</span>
        <div class="flex items-center gap-1">
            <number-flow
                x-ref="dd"
                class="font-bold tabular-nums"
            ></number-flow>
            <span class="text-gray-500 dark:text-gray-400">d</span>
        </div>
        <div class="flex items-center gap-1">
            <number-flow
                x-ref="hh"
                class="font-bold tabular-nums"
            ></number-flow>
            <span class="text-gray-500 dark:text-gray-400">h</span>
        </div>
        <div class="flex items-center gap-1">
            <number-flow
                x-ref="mm"
                class="font-bold tabular-nums"
            ></number-flow>
            <span class="text-gray-500 dark:text-gray-400">m</span>
        </div>
        <div class="flex items-center gap-1">
            <number-flow
                x-ref="ss"
                class="font-bold tabular-nums"
            ></number-flow>
            <span class="text-gray-500 dark:text-gray-400">s</span>
        </div>
    </div>

    {{-- Icon --}}
    <div
        class="hidden transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5 sm:block"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>