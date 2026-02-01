<a
    href="/blog/nativephp-for-mobile-is-now-free"
    onclick="fathom.trackEvent('mobile_free_banner_click');"
    class="group relative z-30 flex flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-emerald-100 via-teal-50 to-cyan-100 px-5 py-3 select-none 3xs:flex-row dark:from-emerald-950/50 dark:via-teal-950/50 dark:to-cyan-950/50"
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
            class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400"
        >
            <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
        </svg>

        {{-- Text --}}
        <div>
            <style>
                .mobile-free-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-emerald-600) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: mobile-free-shine 2s linear infinite;
                }
                .dark .mobile-free-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-emerald-400) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes mobile-free-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="mobile-free-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                <b>NativePHP for Mobile</b> is now <em>completely free</em> and open source!
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
