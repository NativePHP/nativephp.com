<section
    class="mt-5"
    aria-labelledby="sponsors-title"
>
    <div class="dark:bg-mirage rounded-2xl bg-gray-200/60 p-8">
        <div class="flex flex-col gap-1">
            <h2
                id="sponsors-title"
                class="text-2xl font-bold text-gray-800 lg:text-3xl dark:text-white"
            >
                Our Partners
            </h2>
            <h3 class="text-lg text-gray-600 lg:text-xl dark:text-zinc-400">
                NativePHP wouldn't be possible without amazing Partners
            </h3>
        </div>

        <div class="mt-5 flex flex-wrap">
            {{-- Featured partners --}}
            <div
                class="grid grid-cols-[repeat(auto-fill,minmax(15rem,1fr))] gap-5"
            >
                <x-home.featured-sponsor-card
                    sponsorName="BeyondCode"
                    tagline="Essential tools for web developers"
                    href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp"
                >
                    <x-slot:logo>
                        <img
                            src="/img/sponsors/beyondcode.webp"
                            class="block h-auto max-h-20 max-w-full dark:hidden"
                            loading="lazy"
                            alt="BeyondCode logo - PHP development tools and packages"
                            width="160"
                            height="40"
                        />
                        <img
                            src="/img/sponsors/beyondcode-dark.webp"
                            class="hidden h-auto max-h-20 max-w-full dark:block"
                            loading="lazy"
                            alt="BeyondCode logo - PHP development tools and packages"
                            width="160"
                            height="40"
                        />
                    </x-slot>

                    <x-slot:description>
                        From local full stack development to cutting-edge AI
                        platforms, we provide the tools for building your next
                        great app.
                    </x-slot>
                </x-home.featured-sponsor-card>
                <x-home.featured-sponsor-card
                    sponsorName="Laradevs"
                    tagline="Hire the best Laravel developers anywhere"
                    href="https://laradevs.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.laradevs
                            class="block h-auto max-h-10 max-w-full text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </x-slot>

                    <x-slot:description>
                        Need a freelancer or engineer? Laradevs has you covered.
                        Filter by skills, experience, location, availability,
                        and pay.
                    </x-slot>
                </x-home.featured-sponsor-card>
                <x-home.featured-sponsor-card
                    sponsorName="Nexcalia"
                    tagline="Hire the best Laravel developers anywhere"
                    href="https://www.nexcalia.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.nexcalia
                            class="block h-auto max-h-10 max-w-full text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </x-slot>

                    <x-slot:description>
                        Need a freelancer or engineer? Laradevs has you covered.
                        Filter by skills, experience, location, availability,
                        and pay.
                    </x-slot>
                </x-home.featured-sponsor-card>
            </div>
        </div>
    </div>
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
