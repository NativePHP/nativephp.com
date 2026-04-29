<div {!! ! $subscription ? 'wire:poll.2s="loadData"' : '' !!}>
    {{-- Hero Section --}}
    <section
        class="mt-10 px-5 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
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
                    }
                "
                class="text-3xl font-extrabold sm:text-4xl"
            >
                You're In!
            </h1>

            {{-- Introduction Description --}}
            <h2
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                We're excited to have you join the NativePHP community!
            </h2>
        </header>

        {{-- Success Card --}}
        <div
            class="mx-auto mt-10 max-w-xl"
            x-init="
                () => {
                    motion.animate(
                        $el,
                        {
                            opacity: [0, 1],
                            y: [20, 0],
                        },
                        {
                            duration: 0.7,
                            ease: motion.easeOut,
                        },
                    )
                }
            "
        >
            <div
                class="dark:bg-mirage overflow-hidden rounded-xl bg-white p-6 shadow-lg"
            >
                <div class="flex flex-col items-center gap-5 text-center">
                    <div
                        x-ref="checkmark"
                        class="relative grid size-10 place-items-center rounded-full bg-emerald-400 text-black opacity-0 ring-[9px] ring-emerald-400/20"
                        aria-hidden="true"
                        style="transform: none; opacity: 1"
                    >
                        <svg
                            class="size-8"
                            viewBox="0 0 16 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12.466666666666665 4.800013333333333c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0L6.533333333333333 9.799999999999999l-2.0666666666666664 -2.0666666666666664c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0 -0.26666666666666666 0.26666666666666666 -0.26666666666666666 0.6666666666666666 0 0.9333333333333332l2.533333333333333 2.533333333333333c0.13333333333333333 0.13333333333333333 0.26666666666666666 0.19999999999999998 0.4666666666666666 0.19999999999999998 0.19999999999999998 0 0.3333333333333333 -0.06666666666666667 0.4666666666666666 -0.19999999999999998l5.466666666666666 -5.466653333333333c0.26666666666666666 -0.26666666666666666 0.26666666666666666 -0.6666666666666666 0 -0.9333333333333332Z"
                                fill="currentColor"
                                stroke-width="0.6667"
                            ></path>
                        </svg>

                        <div
                            class="absolute top-1/2 right-1/2 hidden size-24 translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-400/20 blur-2xl dark:block"
                            aria-hidden="true"
                        ></div>
                    </div>

                    <div class="space-y-1 dark:text-white">
                        <div
                            x-ref="success_title"
                            class="text-xl font-medium opacity-0"
                            style="transform: none; opacity: 1"
                        >
                            Payment Successful!
                        </div>
                        <div
                            x-ref="success_subtitle"
                            class="text-sm opacity-0"
                            style="transform: none; opacity: 0.5"
                        >
                            Welcome to NativePHP Ultra.
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    @if ($subscription)
                        @if ($isExistingUser)
                            <p
                                class="text-center text-gray-600 dark:text-gray-400"
                            >
                                Your subscription is now active. Head to your
                                dashboard to manage it and access everything
                                included with Ultra.
                            </p>

                            <a
                                href="{{ route('dashboard') }}"
                                class="mt-6 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#d68ffe] dark:text-black dark:hover:bg-[#e1acff]"
                            >
                                Go to Dashboard
                            </a>
                        @else
                            <p
                                class="text-center text-gray-600 dark:text-gray-400"
                            >
                                We've sent a link to
                                <span
                                    class="font-medium dark:text-gray-300"
                                >{{ $email }}</span>
                                so you can claim your account and access your
                                dashboard.
                            </p>

                            <p
                                class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400"
                            >
                                Didn't get the email? Check your spam folder, or
                                reach out to
                                <a
                                    href="mailto:support@nativephp.com"
                                    class="font-medium text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300"
                                >support@nativephp.com</a>
                                and we'll sort it out.
                            </p>
                        @endif
                    @else
                        <div
                            class="mt-6 flex items-center justify-center gap-2"
                        >
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                                style="animation-delay: 0.2s"
                            ></div>
                            <div
                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-blue-500"
                                style="animation-delay: 0.4s"
                            ></div>
                            <span
                                class="ml-2 text-lg text-gray-600 dark:text-gray-400"
                            >
                                Finalising your subscription
                            </span>
                        </div>
                        <p
                            class="mt-6 text-center text-gray-600 dark:text-gray-400"
                        >
                            This will only take a moment. The page will update
                            automatically.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Next Steps --}}
        <div class="mx-auto mt-16 max-w-2xl">
            <h3
                class="text-center text-2xl font-semibold"
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                delay: 0.3,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
            >
                What's Next?
            </h3>

            <div
                class="mt-8 grid gap-6 md:grid-cols-2"
                x-init="
                    () => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                opacity: [0, 1],
                                y: [20, 0],
                            },
                            {
                                duration: 0.5,
                                delay: motion.stagger(0.1, { start: 0.4 }),
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
            >
                <a
                    href="/docs/mobile/1/getting-started/installation"
                    class="dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-violet-100 dark:bg-violet-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-violet-600 dark:text-violet-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                            <path d="M2 17l10 5 10-5"></path>
                            <path d="M2 12l10 5 10-5"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Install the Package</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Follow our step-by-step guide to install and set up
                        NativePHP in your Laravel project.
                    </p>
                </a>

                <a
                    href="{{ $discordLink }}"
                    class="dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-indigo-600 dark:text-indigo-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                            ></path>
                            <circle
                                cx="9"
                                cy="7"
                                r="4"
                            ></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Join Our Community</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Connect with other developers, get help, and share your
                        experiences in our Discord community.
                    </p>
                </a>

                <div
                    class="dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud rounded-2xl bg-gray-100 p-6 transition duration-200 hover:bg-gray-200/70 hover:ring-1 hover:ring-black/60"
                >
                    <div
                        class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-6 text-emerald-600 dark:text-emerald-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <polyline
                                points="22 12 18 12 15 21 9 3 6 12 2 12"
                            ></polyline>
                        </svg>
                    </div>
                    <h4 class="text-lg font-medium">Build Your First App</h4>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Follow our tutorials to create your first mobile app
                        using PHP and Laravel. Coming soon.
                    </p>
                </div>

            </div>
        </div>
    </section>
</div>
