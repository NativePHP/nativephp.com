<x-layout>
    {{-- Hero Section --}}
    <section class="mx-auto mt-12 max-w-5xl px-5">
        <div class="grid place-items-center text-center">
            {{-- Illustration --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
            >
                <x-illustrations.partnership class="mx-auto w-48" />
            </div>

            {{-- Title --}}
            <h1
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-5 text-4xl md:text-5xl lg:text-6xl"
            >
                <span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">
                    {
                </span>
                <span class="font-bold">Partner</span>
                <span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">
                    }
                </span>
                <span class="font-semibold">with NativePHP</span>
            </h1>

            {{-- Subtitle --}}
            <p
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto mt-6 max-w-3xl text-lg text-gray-600 dark:text-zinc-400"
            >
                We're helping teams just like yours build beautiful mobile and desktop apps faster
                than ever!
            </p>

            {{-- Call to Action Buttons --}}
            <div
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
                                    delay: motion.stagger(0.2),
                                },
                            )
                        })
                    }
                "
                class="mt-8 flex w-full flex-col items-center justify-center gap-4 sm:flex-row"
            >
                {{-- Primary CTA - Email --}}
                <div class="w-full max-w-56">
                    <a
                        href="mailto:partners@nativephp.com?subject=Interested%20In%20Being%20a%20Partner"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                            ></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Email us
                    </a>
                </div>

                {{-- Secondary CTA - Calendar --}}
                <div class="w-full max-w-56">
                    <a
                        href="https://cal.com/team/nativephp/partners"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-gray-200 px-6 py-4 text-gray-800 transition duration-200 hover:bg-gray-300/80 dark:bg-slate-700/30 dark:text-white dark:hover:bg-slate-700/40"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="18"
                                rx="2"
                                ry="2"
                            ></rect>
                            <line
                                x1="16"
                                y1="2"
                                x2="16"
                                y2="6"
                            ></line>
                            <line
                                x1="8"
                                y1="2"
                                x2="8"
                                y2="6"
                            ></line>
                            <line
                                x1="3"
                                y1="10"
                                x2="21"
                                y2="10"
                            ></line>
                        </svg>
                        Book a Meeting
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Partnership Details Section --}}
    <section class="mx-auto mt-24 max-w-5xl px-5">
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                y: [10, 0],
                                opacity: [0, 1],
                                scale: [0.8, 1],
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
            class="grid gap-x-8 gap-y-12 md:grid-cols-2 lg:grid-cols-3"
        >
            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Dedicated Support</x-slot>

                <x-slot name="description">
                    Get access to the creators of NativePHP, with dedicated
                    support channels and faster response times.
                </x-slot>
            </x-benefit-card>

            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Priority Feature Development</x-slot>

                <x-slot name="description">
                    Have a direct line to our development team and
                    influence our feature development to ensure your business
                    needs are met.
                </x-slot>
            </x-benefit-card>

            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Training & Onboarding</x-slot>

                <x-slot name="description">
                    Comprehensive training and onboarding for your
                    team to get them up to speed quickly.
                </x-slot>
            </x-benefit-card>

            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Ultra Licensing</x-slot>

                <x-slot name="description">
                    Receive an Ultra license for: unlimited published
                    applications, unlimited developers, and team management
                    tools.
                </x-slot>
            </x-benefit-card>

            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Development Support</x-slot>

                <x-slot name="description">
                    Just need someone to build your app? We'll be delighted to!
                </x-slot>
            </x-benefit-card>

            {{-- Card --}}
            <x-benefit-card>
                <x-slot name="icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="size-6"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"
                        />
                    </svg>
                </x-slot>

                <x-slot name="title">Strategic Partnership</x-slot>

                <x-slot name="description">
                    Become a strategic partner in the NativePHP ecosystem with
                    co-marketing opportunities.
                </x-slot>
            </x-benefit-card>
        </div>
    </section>

    {{-- Ideal Partners Section --}}
    <section class="mx-auto mt-16 max-w-5xl px-5">
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
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="rounded-2xl bg-gray-100 p-12 dark:bg-[#1a1a2e]"
        >
            <h2 class="text-3xl font-semibold">Who Should Partner With Us?</h2>

            <div class="mt-8 space-y-5">
                <div class="flex gap-3.5">
                    <div
                        class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium">
                            App Development Companies
                        </h3>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            Businesses building mobile applications that want to
                            leverage PHP and Laravel expertise.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3.5">
                    <div
                        class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium">Digital Agencies</h3>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            Agencies looking to expand their service offerings
                            with native mobile app development.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3.5">
                    <div
                        class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5 shrink-0"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-medium">
                            Freelance Developers
                        </h3>
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            PHP/Laravel freelancers who want to offer native
                            mobile app development to their clients.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section class="mx-auto mt-16 max-w-5xl px-5 pb-24">
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
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="rounded-2xl bg-gray-100 p-10 text-center dark:bg-mirage"
        >
            <h2 class="text-3xl font-semibold">Ready to Partner With Us?</h2>

            <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-gray-400">
                Contact our team to discuss how a partnership with NativePHP can
                benefit your business and help you deliver exceptional
                applications.
            </p>

            <div
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
                                    delay: motion.stagger(0.2),
                                },
                            )
                        })
                    }
                "
                class="mt-6 flex w-full flex-col items-center justify-center gap-4 sm:flex-row"
            >
                {{-- Primary CTA - Email --}}
                <div class="w-full max-w-56">
                    <a
                        href="mailto:partners@nativephp.com?subject=Interested%20In%20Being%20a%20Partner"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                            ></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Email us
                    </a>
                </div>

                {{-- Secondary CTA - Calendar --}}
                <div class="w-full max-w-56">
                    <a
                        href="https://cal.com/team/nativephp/partners"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-gray-200 px-6 py-4 text-gray-800 transition duration-200 hover:bg-gray-300/80 dark:bg-slate-700/30 dark:text-white dark:hover:bg-slate-700/40"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="18"
                                rx="2"
                                ry="2"
                            ></rect>
                            <line
                                x1="16"
                                y1="2"
                                x2="16"
                                y2="6"
                            ></line>
                            <line
                                x1="8"
                                y1="2"
                                x2="8"
                                y2="6"
                            ></line>
                            <line
                                x1="3"
                                y1="10"
                                x2="21"
                                y2="10"
                            ></line>
                        </svg>
                        Book a Meeting
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
