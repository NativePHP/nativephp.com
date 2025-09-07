<a
    href="https://www.producthunt.com/"
    onclick="fathom.trackEvent('alert_click');"
    class="group relative z-30 flex flex-wrap items-center justify-center gap-x-2 gap-y-2.5 overflow-hidden bg-gray-100 px-5 py-2 text-center text-sm tracking-tight text-pretty select-none [--blue-ribbon:#3B63FE] [--product-hunt:#FF6154] sm:text-base dark:bg-gray-950/50"
    aria-label="Bifrost dropped on Product Hunt. Please upvote."
>
    {{-- Decorative left arrows --}}
    <div
        class="hidden flex-row-reverse gap-2 min-[31rem]:flex"
        aria-hidden="true"
    >
        <svg
            x-data="{
                init() {
                    gsap.to($el, {
                        keyframes: { autoAlpha: [0, 1, 0], y: [0, -10] },
                        repeat: -1,
                        ease: 'sine.in',
                        duration: 2,
                    })
                },
            }"
            xmlns="http://www.w3.org/2000/svg"
            class="w-2.5 mask-b-from-20% text-[var(--product-hunt)]"
            viewBox="0 0 9 16"
            fill="none"
            aria-hidden="true"
            focusable="false"
        >
            <path
                d="M4.5 0.749999L0.169873 8.25L8.83013 8.25L4.5 0.749999ZM4.5 15.75L5.25 15.75L5.25 7.5L4.5 7.5L3.75 7.5L3.75 15.75L4.5 15.75Z"
                fill="currentColor"
            />
        </svg>
        <svg
            x-data="{
                init() {
                    gsap.to($el, {
                        keyframes: { autoAlpha: [0, 1, 0], y: [8, 0] },
                        repeat: -1,
                        ease: 'sine.in',
                        duration: 2,
                    })
                },
            }"
            xmlns="http://www.w3.org/2000/svg"
            class="w-3 mask-b-from-20% text-[var(--blue-ribbon)]"
            viewBox="0 0 9 16"
            fill="none"
            aria-hidden="true"
            focusable="false"
        >
            <path
                d="M4.5 0.749999L0.169873 8.25L8.83013 8.25L4.5 0.749999ZM4.5 15.75L5.25 15.75L5.25 7.5L4.5 7.5L3.75 7.5L3.75 15.75L4.5 15.75Z"
                fill="currentColor"
            />
        </svg>
    </div>

    {{-- Text: part 1 --}}
    <span
        class="transition duration-200 ease-out will-change-transform group-hover:translate-x-0.5 dark:text-slate-200"
    >
        Bifrost dropped on
    </span>

    {{-- Product Hunt badge --}}
    <span
        class="flex items-center gap-1.5 rounded-full bg-white/50 py-1 pr-3 pl-1 transition duration-200 ease-out will-change-transform group-hover:scale-95 dark:bg-black/50"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-6 shrink-0"
            viewBox="0 0 21 20"
            fill="none"
            aria-hidden="true"
            focusable="false"
        >
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M20.5 10C20.5 15.523 16.023 20 10.5 20C4.977 20 0.5 15.523 0.5 10C0.5 4.477 4.977 0 10.5 0C16.023 0 20.5 4.477 20.5 10Z"
                fill="currentColor"
                class="text-[var(--product-hunt)]"
            />
            <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M11.8335 10H9V7H11.8335C12.2313 7 12.6129 7.15804 12.8942 7.43934C13.1755 7.72064 13.3335 8.10218 13.3335 8.5C13.3335 8.89782 13.1755 9.27936 12.8942 9.56066C12.6129 9.84196 12.2313 10 11.8335 10ZM11.8335 5H7V15H9V12H11.8335C12.7618 12 13.652 11.6313 14.3084 10.9749C14.9648 10.3185 15.3335 9.42826 15.3335 8.5C15.3335 7.57174 14.9648 6.6815 14.3084 6.02513C13.652 5.36875 12.7618 5 11.8335 5Z"
                fill="white"
            />
        </svg>
        <span class="font-medium whitespace-nowrap text-[var(--product-hunt)]">
            Product Hunt
        </span>
    </span>

    {{-- Text: part 2 --}}
    <span
        class="w-full transition duration-200 ease-out will-change-transform group-hover:-translate-x-0.5 xs:w-auto dark:text-slate-200"
    >
        Please upvote!
    </span>

    {{-- Decorative right arrows --}}
    <div
        class="hidden gap-2 min-[31rem]:flex"
        aria-hidden="true"
    >
        <svg
            x-data="{
                init() {
                    gsap.to($el, {
                        keyframes: { autoAlpha: [0, 1, 0], y: [0, -10] },
                        repeat: -1,
                        ease: 'sine.in',
                        duration: 2,
                    })
                },
            }"
            xmlns="http://www.w3.org/2000/svg"
            class="w-2.5 mask-b-from-20% text-[var(--product-hunt)]"
            viewBox="0 0 9 16"
            fill="none"
            aria-hidden="true"
            focusable="false"
        >
            <path
                d="M4.5 0.749999L0.169873 8.25L8.83013 8.25L4.5 0.749999ZM4.5 15.75L5.25 15.75L5.25 7.5L4.5 7.5L3.75 7.5L3.75 15.75L4.5 15.75Z"
                fill="currentColor"
            />
        </svg>
        <svg
            x-data="{
                init() {
                    gsap.to($el, {
                        keyframes: { autoAlpha: [0, 1, 0], y: [8, 0] },
                        repeat: -1,
                        ease: 'sine.in',
                        duration: 2,
                    })
                },
            }"
            xmlns="http://www.w3.org/2000/svg"
            class="w-3 mask-b-from-20% text-[var(--blue-ribbon)]"
            viewBox="0 0 9 16"
            fill="none"
            aria-hidden="true"
            focusable="false"
        >
            <path
                d="M4.5 0.749999L0.169873 8.25L8.83013 8.25L4.5 0.749999ZM4.5 15.75L5.25 15.75L5.25 7.5L4.5 7.5L3.75 7.5L3.75 15.75L4.5 15.75Z"
                fill="currentColor"
            />
        </svg>
    </div>

    {{-- Decorative blurs --}}
    <div
        class="absolute -top-5 right-1/2 -z-10 translate-x-1/2"
        aria-hidden="true"
    >
        <div
            class="h-12 w-40 -translate-x-20 rotate-30 rounded-full bg-[var(--product-hunt)] blur-[30px] dark:bg-red-500/60"
        ></div>
    </div>
    <div
        class="absolute -top-5 right-1/2 -z-10 translate-x-1/2"
        aria-hidden="true"
    >
        <div
            class="h-12 w-40 translate-x-20 rotate-30 rounded-full bg-[var(--blue-ribbon)] blur-[30px] dark:bg-blue-400/60"
        ></div>
    </div>
</a>
