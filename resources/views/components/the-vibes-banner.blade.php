<a
    href="/the-vibes"
    onclick="fathom.trackEvent('the_vibes_banner_click');"
    data-site-banner
    class="group relative z-30 flex flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-violet-100 via-indigo-50 to-violet-100 px-5 py-3 select-none 3xs:flex-row dark:from-violet-950/50 dark:via-indigo-950/50 dark:to-violet-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="size-5 shrink-0 text-violet-600 dark:text-violet-400"
        >
            <path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z" clip-rule="evenodd" />
        </svg>

        {{-- Text --}}
        <div>
            <style>
                .vibes-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-violet-600) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: vibes-shine 2s linear infinite;
                }
                .dark .vibes-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-violet-400) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes vibes-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="vibes-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                <b>The Vibes</b> &mdash; the unofficial Laracon US Day 3 event. Early Bird tickets available until March 31!
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
