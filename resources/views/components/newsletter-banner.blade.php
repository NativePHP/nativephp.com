<button
    type="button"
    x-on:click="
        $dispatch('open-newsletter-modal')
        window.fathom?.trackEvent('newsletter_banner_click')
    "
    data-site-banner
    class="group relative z-30 flex w-full flex-col items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-emerald-100 via-lime-50 to-emerald-100 px-5 py-3 select-none 3xs:flex-row dark:from-emerald-950/50 dark:via-lime-950/50 dark:to-emerald-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.gift
            class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400"
            aria-hidden="true"
        />

        {{-- Text --}}
        <div>
            <style>
                .newsletter-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-black) 0%,
                        var(--color-emerald-600) 35%,
                        var(--color-black) 70%
                    );
                    background-size: 200% 100%;
                    animation: newsletter-shine 2s linear infinite;
                }
                .dark .newsletter-gradient-text {
                    background-image: linear-gradient(
                        90deg,
                        var(--color-white) 0%,
                        var(--color-emerald-400) 35%,
                        var(--color-white) 70%
                    );
                }
                @keyframes newsletter-shine {
                    from {
                        background-position: 200% center;
                    }
                    to {
                        background-position: 0% center;
                    }
                }
            </style>
            <div
                class="newsletter-gradient-text bg-clip-text tracking-tight text-pretty text-transparent sm:text-center"
            >
                Celebrate
                <b>SuperNative!</b>
                Join the newsletter. Get
                <b>10% off everything</b>
            </div>
        </div>
    </div>

    {{-- Arrow --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</button>
