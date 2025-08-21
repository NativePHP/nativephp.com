<section
    class="mx-auto mt-20 max-w-5xl"
    aria-labelledby="sponsors-title"
>
    <h2
        id="sponsors-title"
        class="sr-only"
    >
        NativePHP Sponsors
    </h2>
    <div class="divide-y divide-[#242A2E]/20 *:py-8">
        {{-- Featured sponsors --}}
        <div
            class="flex flex-col items-center justify-between gap-x-10 gap-y-5 md:flex-row md:items-start"
            aria-labelledby="featured-sponsors-title"
        >
            <h3
                id="featured-sponsors-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="shrink-0 text-xl font-medium opacity-0"
            >
                Featured Sponsors
            </h3>
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $refAll('sponsor'),
                                {
                                    scale: [0, 1],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.1),
                                },
                            )
                        })
                    }
                "
                class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                aria-label="Featured sponsors of the NativePHP project"
            >
                <x-sponsors.lists.home.featured-sponsors />
            </div>
        </div>
        {{-- Corporate sponsors --}}
        <div
            class="flex flex-col items-center justify-between gap-x-10 gap-y-5 md:flex-row md:items-start"
            aria-labelledby="corporate-sponsors-title"
        >
            <h3
                id="corporate-sponsors-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="shrink-0 text-xl font-medium opacity-0"
            >
                Corporate Sponsors
            </h3>
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $refAll('sponsor'),
                                {
                                    scale: [0, 1],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.1),
                                },
                            )
                        })
                    }
                "
                class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                aria-label="Corporate sponsors of the NativePHP project"
            >
                <x-sponsors.lists.home.corporate-sponsors />
            </div>
        </div>
    </div>
    <div
        x-init="
            () => {
                motion.inView(
                    $el,
                    (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [-50, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                            },
                        )
                    },
                    {
                        amount: 0.5,
                    },
                )
            }
        "
        class="opacity-0 will-change-transform"
    >
        <a
            href="/docs/getting-started/sponsoring"
            class="group dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud flex flex-wrap items-center justify-center gap-x-5 gap-y-3 rounded-3xl bg-gray-100 px-8 py-8 transition duration-200 ease-in-out hover:ring-1 hover:ring-black/60 md:justify-between md:px-12 md:py-10"
            aria-label="Learn about sponsoring the NativePHP project"
        >
            <div
                class="inline-flex shrink-0 flex-col-reverse items-center gap-x-5 gap-y-3 md:flex-row"
            >
                <div class="space-y-2 text-2xl font-medium">
                    <span>Want</span>
                    <span>your logo</span>
                    <span>here?</span>
                </div>
                {{-- Arrow --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    class="w-12 -rotate-45 transition duration-300 ease-out will-change-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 md:w-16 md:rotate-0"
                    aria-hidden="true"
                >
                    <path
                        class="text-[#e8e4f8] dark:text-[#31416e]/30"
                        fill="currentColor"
                        d="M12 22c5.5228 0 10 -4.4772 10 -10 0 -5.52285 -4.4772 -10 -10 -10C6.47715 2 2 6.47715 2 12c0 5.5228 4.47715 10 10 10Z"
                        stroke-width="1"
                    ></path>
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14.499 2.49707h7v7"
                        stroke-width="1"
                    ></path>
                    <path
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21.499 2.49707 5.49902 18.4971"
                        stroke-width="1"
                    ></path>
                </svg>
            </div>
            <div
                class="text-center font-light opacity-50 md:max-w-xs md:text-left md:text-lg"
            >
                Become a sponsor and get your logo on our README on GitHub with
                a link to your site.
            </div>
        </a>
    </div>
</section>
