<a
    href="{{ route('pricing') }}#pricing"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-tl from-teal-100 to-gray-200/60 px-5 py-3 select-none 3xs:flex-row dark:from-sky-950/65 dark:to-gray-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.banner.dollar-decrease
            class="h-5 shrink-0 dark:text-cyan-200"
        />

        {{-- Text --}}
        <div>
            <style>
                .gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-teal-300) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: shine 2s linear infinite;
                }
                .dark .gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-sky-300) 35%,
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
                class="gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                New lower license prices available now!
            </div>
        </div>
    </div>

    {{-- Icon --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>
