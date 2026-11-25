<a
    href="https://bifrost-technologies.kit.com/03a1490f56"
    target="_blank"
    rel="noopener"
    onclick="window.fathom?.trackEvent('bifrost_waitlist_banner_click')"
    data-site-banner
    class="group relative z-30 flex flex-col flex-wrap items-center justify-center gap-x-3 gap-y-2.5 overflow-hidden bg-gradient-to-r from-sky-50 via-indigo-50 to-violet-100 px-5 py-3 select-none 3xs:flex-row dark:from-sky-950/50 dark:via-indigo-950/50 dark:to-violet-950/50"
>
    {{-- Label --}}
    <div
        class="flex items-center justify-center gap-3 transition duration-200 ease-in-out will-change-transform group-hover:-translate-x-0.5"
    >
        {{-- Icon --}}
        <x-icons.rocket
            class="size-5 shrink-0 text-indigo-600 dark:text-indigo-300"
            aria-hidden="true"
        />

        {{-- Text --}}
        <div class="tracking-tight text-pretty sm:text-center">
            <b>Ship your PHP app to the App Store without the usual pain.</b>
            <span class="text-gray-600 dark:text-gray-300">Free guides from the Bifrost team.</span>
        </div>
    </div>

    {{-- Call to action --}}
    <span
        class="shrink-0 rounded-md bg-white/70 px-2 py-0.5 text-sm font-semibold text-indigo-900 ring-1 ring-indigo-200 dark:bg-white/10 dark:text-indigo-100 dark:ring-white/10"
    >
        Get the free guides
    </span>

    {{-- Arrow --}}
    <div
        class="transition duration-200 ease-in-out will-change-transform group-hover:translate-x-0.5"
    >
        <x-icons.right-arrow class="size-3 shrink-0" />
    </div>
</a>
