<footer
    class="mx-auto max-w-5xl px-5 pt-20 pb-5 xl:max-w-7xl 2xl:max-w-360"
    aria-labelledby="footer-heading"
>
    <h2
        id="footer-heading"
        class="sr-only"
    >
        Footer
    </h2>
    <div
        class="flex flex-col flex-wrap gap-x-6 gap-y-4 md:flex-row md:items-end md:justify-between"
    >
        {{-- Left side --}}
        <div class="flex flex-col items-center gap-6 md:items-start">
            {{-- Logo --}}
            <div
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
                class="opacity-0"
            >
                <a
                    href="/"
                    class="transition duration-200 will-change-transform hover:scale-[1.02]"
                >
                    <x-logo
                        class="h-6"
                        aria-hidden="true"
                    />
                    <span class="sr-only">NativePHP homepage</span>
                </a>
            </div>
            {{-- Social links --}}
            <nav
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [10, 0],
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
                class="flex flex-wrap items-center justify-center gap-2.5 *:opacity-0"
                aria-label="Social networks"
            >
                <x-social-networks-all />
            </nav>

            {{-- Newsletter --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
            >
                <a
                    href="/newsletter"
                    class="group dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud relative z-0 flex max-w-105 items-center gap-6 overflow-hidden rounded-2xl bg-cyan-50/50 py-5 pr-7 pl-6 ring-1 ring-black/5 transition duration-300 ease-in-out hover:bg-cyan-50 hover:ring-black/10"
                >
                    {{-- Decorative circle --}}
                    <div
                        class="absolute top-1/2 left-3 -z-10 size-16 -translate-y-1/2 rounded-full bg-cyan-400/60 blur-2xl dark:block"
                        aria-hidden="true"
                    ></div>

                    {{-- Content --}}
                    <div class="flex items-center gap-5 text-sm">
                        <div class="flex flex-col items-center gap-2">
                            {{-- Icon --}}
                            <x-icons.email-document
                                aria-hidden="true"
                                class="size-7 shrink-0"
                            />

                            {{-- Title --}}
                            <span
                                class="transition duration-300 will-change-transform group-hover:scale-105"
                            >
                                Newsletter
                            </span>
                        </div>

                        {{-- Message --}}
                        <p
                            class="leading-relaxed opacity-70 transition duration-300 will-change-transform group-hover:translate-x-0.5"
                        >
                            Get the latest NativePHP updates and news delivered
                            to your inbox.
                        </p>
                    </div>

                    {{-- Right arrow --}}
                    <x-icons.right-arrow
                        x-init="
                        () => {
                            motion.animate(
                                $el,
                                {
                                    x: [0, 10],
                                },
                                {
                                    repeat: Infinity,
                                    repeatType: 'reverse',
                                    type: 'spring',
                                    stiffness: 100,
                                    damping: 20
                                },
                            )
                        }
                    "
                        aria-hidden="true"
                        class="size-4 shrink-0"
                    />
                </a>
            </div>
        </div>

        {{-- Right side --}}
        <nav
            class="flex w-full flex-wrap justify-center gap-x-5 gap-y-3 sm:w-auto lg:justify-around xl:gap-x-10"
            aria-label="Footer navigation"
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                x: [-10, 0],
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
        >
            {{-- Column --}}
            <section
                class="flex grow flex-col items-start gap-1 sm:grow-0"
                aria-labelledby="footer-explore-heading"
            >
                <h3
                    id="footer-explore-heading"
                    class="font-medium"
                >
                    Explore
                </h3>
                <ul
                    class="flex flex-col items-start text-sm text-gray-500 dark:text-gray-400"
                >
                    <li>
                        <a
                            href="{{ route('welcome') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Home
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('blog') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Blog
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://shop.nativephp.com/"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Shop
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://bifrost.nativephp.com/"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Bifrost
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('privacy-policy') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Privacy Policy
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('terms-of-service') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Terms of Service
                        </a>
                    </li>
                </ul>
            </section>

            {{-- Column --}}
            <section
                class="flex grow flex-col items-start gap-1 sm:grow-0"
                aria-labelledby="footer-mobile-heading"
            >
                <h3
                    id="footer-mobile-heading"
                    class="font-medium"
                >
                    Mobile
                </h3>
                <ul
                    class="flex flex-col items-start text-sm text-gray-500 dark:text-gray-400"
                >
                    <li>
                        <a
                            href="/docs/mobile/1/getting-started/introduction"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Documentation
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('pricing') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Pricing
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://github.com/nativephp"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            GitHub
                        </a>
                    </li>
                </ul>
            </section>

            {{-- Column --}}
            <section
                class="flex grow flex-col items-start gap-1 sm:grow-0"
                aria-labelledby="footer-desktop-heading"
            >
                <h3
                    id="footer-desktop-heading"
                    class="font-medium"
                >
                    Desktop
                </h3>
                <ul
                    class="flex flex-col items-start text-sm text-gray-500 dark:text-gray-400"
                >
                    <li>
                        <a
                            href="/docs/desktop/1/getting-started/introduction"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Documentation
                        </a>
                    </li>
                    <li>
                        <a
                            href="{{ route('sponsoring') }}"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            Sponsoring
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://github.com/nativephp"
                            class="inline-block px-px py-1.5 transition duration-300 will-change-transform hover:translate-x-1 hover:text-gray-700 dark:hover:text-gray-300"
                        >
                            GitHub
                        </a>
                    </li>
                </ul>
            </section>
        </nav>
    </div>

    {{-- Divider --}}
    <div
        class="flex items-center pt-3 pb-3"
        aria-hidden="true"
    >
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
        <div class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"></div>
    </div>

    {{-- Copyright --}}
    <section
        class="flex flex-col flex-wrap items-center gap-x-5 gap-y-3 text-center text-sm text-gray-500 md:flex-row md:justify-between md:text-left xl:gap-x-10 dark:text-gray-400/80"
        aria-label="Credits and copyright information"
    >
        <div
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
            class="flex flex-col flex-wrap items-center gap-2 opacity-0 md:flex-row"
        >
            <div class="flex gap-1">
                <div>Website designed by</div>
                <a
                    href="https://zahirnia.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group relative font-medium text-black/80 transition duration-200 hover:text-black dark:text-white/80 dark:hover:text-white"
                >
                    Hassan Zahirnia
                    <div
                        class="absolute -bottom-0.5 left-0 h-px w-full origin-right scale-x-0 bg-current transition duration-300 ease-out will-change-transform group-hover:origin-left group-hover:scale-x-100"
                    ></div>
                </a>
            </div>
            <div
                class="hidden h-3 w-0.5 bg-gray-300 md:block dark:bg-[#242734]"
            ></div>
            <div class="flex gap-1">
                <div>Logo by</div>
                <a
                    href="https://x.com/caneco"
                    target="_blank"
                    class="group relative font-medium text-black/80 transition duration-200 hover:text-black dark:text-white/80 dark:hover:text-white"
                    rel="noopener noreferrer"
                >
                    Caneco
                    <div
                        class="absolute -bottom-0.5 left-0 h-px w-full origin-right scale-x-0 bg-current transition duration-300 ease-out will-change-transform group-hover:origin-left group-hover:scale-x-100"
                    ></div>
                </a>
            </div>
        </div>
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                            },
                        )
                    })
                }
            "
            class="opacity-0"
        >
            <span>© {{ date('Y') }} Bifrost Technology, LLC</span>
        </div>
    </section>
</footer>
