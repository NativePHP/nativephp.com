<x-layout title="Laracon US 2025 Ticket Giveaway">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
    {{-- Hero Section --}}
    <section class="mx-auto mt-2 max-w-7xl px-5">
        <div
            x-ref="ticketEvent"
            class="grid place-items-center py-10 text-center"
        >
            {{-- Countdown Header --}}
            <h2
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-5, 0],
                                    y: [-5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="font-medium text-gray-600 sm:text-lg dark:text-gray-400"
            >
                Hurry! Entry Closes In:
            </h2>

            {{-- Countdown Timer --}}
            <div
                x-data="countdown('2025-07-01T00:00:00Z')"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [5, 0],
                                    y: [5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-2 grid grid-cols-4 gap-5 text-4xl sm:gap-10 sm:text-5xl"
            >
                <div class="flex flex-col items-center">
                    <number-flow
                        x-ref="dd"
                        class="font-bold"
                    ></number-flow>
                    <div
                        class="text-sm text-slate-600 uppercase sm:text-base dark:text-white/60"
                    >
                        Days
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <number-flow
                        x-ref="hh"
                        class="font-bold"
                    ></number-flow>
                    <div
                        class="text-sm text-slate-600 uppercase sm:text-base dark:text-white/60"
                    >
                        Hours
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <number-flow
                        x-ref="mm"
                        class="font-bold"
                    ></number-flow>
                    <div
                        class="text-sm text-slate-600 uppercase sm:text-base dark:text-white/60"
                    >
                        Minutes
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <number-flow
                        x-ref="ss"
                        class="font-bold"
                    ></number-flow>
                    <div
                        class="text-sm text-slate-600 uppercase sm:text-base dark:text-white/60"
                    >
                        Seconds
                    </div>
                </div>
            </div>

            {{-- Ticket --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative isolate py-7 sm:py-10"
            >
                <div
                    x-init="
                        () => {
                            Atropos({
                                el: $el,
                                activeOffset: 1,
                                rotateXMax: 15,
                                rotateYMax: 13,
                                shadow: false,
                                highlight: false,
                                eventsEl: $refs.ticketEvent,
                            })
                        }
                    "
                    class="atropos"
                >
                    <div class="atropos-scale">
                        <div class="atropos-rotate">
                            <div class="atropos-inner">
                                <img
                                    src="{{ Vite::asset('resources/images/laracon-us-2025/ticket.webp') }}"
                                    alt="Laracon US 2025 Ticket"
                                    class="w-full max-w-130"
                                />
                                <img
                                    data-atropos-offset="8"
                                    src="{{ Vite::asset('resources/images/laracon-us-2025/laracon-text.webp') }}"
                                    alt="Laracon"
                                    class="absolute right-[23vw] bottom-[4vw] w-[40vw] sm:right-34 sm:bottom-6 sm:w-58"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute top-1/2 right-1/2 -z-10 hidden h-full w-full translate-x-1/2 -translate-y-1/2 rounded-full bg-slate-500/25 blur-3xl dark:block"
                ></div>
            </div>

            <header class="relative z-10 grid place-items-center text-center">
                {{-- Primary Heading --}}
                <h1
                    id="hero-heading"
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
                    class="text-3xl font-extrabold sm:text-4xl"
                >
                    Ticket Giveaway
                </h1>

                {{-- Introduction Description --}}
                <h2
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
                    class="mx-auto max-w-2xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
                >
                    Laracon US is an annual gathering of people who are
                    passionate about building amazing applications with the
                    Laravel web framework.
                </h2>

                {{-- Primary CTA - Email --}}
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        y: [10, 0],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mt-7 w-full max-w-56"
                >
                    <a
                        href="#how-to-enter"
                        class="flex items-center justify-center gap-2.5 rounded-2xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-violet-400/80 dark:drop-shadow-xl dark:drop-shadow-transparent dark:hover:bg-violet-400 dark:hover:drop-shadow-violet-400/30"
                    >
                        Enter to Win
                    </a>
                </div>
            </header>
        </div>
    </section>

    {{-- Prizes --}}
    <section class="mx-auto mt-20 max-w-5xl px-5">
        {{-- Header --}}
        <h2
            id="hero-heading"
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
            class="text-center text-3xl font-extrabold sm:text-4xl"
        >
            Prizes
        </h2>

        {{-- List --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                y: [20, 0],
                                opacity: [0, 1],
                            },
                            {
                                duration: 1,
                                ease: motion.backOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
            class="mt-10 flex flex-col items-center justify-start gap-8 lg:flex-row lg:items-start"
        >
            <div
                class="flex flex-col items-center gap-x-7 gap-y-5 sm:flex-row lg:flex-col"
            >
                {{-- Card --}}
                <div
                    class="group relative isolate flex w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-violet-50 to-violet-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
                >
                    {{-- Title --}}
                    <h5
                        class="text-2xl leading-relaxed font-semibold text-violet-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-violet-400"
                    >
                        Laracon
                        <br />
                        Ticket
                    </h5>
                    {{-- Illustration --}}
                    <img
                        src="{{ Vite::asset('resources/images/prizes/3d_purple_tickets.webp') }}"
                        alt=""
                        loading="lazy"
                        class="pointer-events-none h-16 transition duration-300 ease-in-out will-change-transform select-none group-hover:-translate-x-0.5"
                    />
                    {{-- Shiny circle --}}
                    <div
                        class="absolute -top-40 -right-40 -z-10 size-80 rounded-full bg-gradient-to-t from-white/5 to-white/50 dark:from-transparent dark:to-violet-500/65 dark:blur-2xl"
                    ></div>
                </div>

                {{-- Description --}}
                <div class="flex shrink-0 flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/gold_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            1st Place
                        </h6>
                    </div>
                </div>
            </div>
            <div
                class="flex flex-col items-center gap-x-7 gap-y-5 sm:flex-row lg:flex-col"
            >
                {{-- Card --}}
                <div
                    class="group relative isolate flex w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 to-sky-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
                >
                    {{-- Title --}}
                    <h5
                        class="text-2xl leading-relaxed font-semibold text-sky-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-sky-300"
                    >
                        NativePHP
                        <br />
                        T-Shirt
                    </h5>
                    {{-- Illustration --}}
                    <img
                        src="{{ Vite::asset('resources/images/prizes/nativephp_black_shirt.webp') }}"
                        alt=""
                        loading="lazy"
                        class="pointer-events-none relative right-2 -mb-20 h-40 transition duration-300 ease-in-out will-change-transform select-none group-hover:-translate-x-0.5 dark:contrast-120"
                    />
                    {{-- Shiny circle --}}
                    <div
                        class="absolute -top-40 -right-40 -z-10 size-80 rounded-full bg-gradient-to-t from-white/5 to-white/50 dark:from-transparent dark:to-sky-500/65 dark:blur-2xl"
                    ></div>
                </div>

                {{-- Description --}}
                <div class="flex shrink-0 flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/gold_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            1st Place
                        </h6>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/silver_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            2nd Place
                        </h6>
                    </div>
                </div>
            </div>
            <div
                class="flex flex-col items-center gap-x-7 gap-y-5 sm:flex-row lg:flex-col"
            >
                <div
                    class="group relative isolate flex w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-orange-50 to-orange-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
                >
                    {{-- Title --}}
                    <h5
                        class="text-2xl leading-relaxed font-semibold text-orange-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-orange-400"
                    >
                        NativePHP
                        <br />
                        License
                    </h5>
                    {{-- Illustration --}}
                    <img
                        src="{{ Vite::asset('resources/images/prizes/3d_license_document.webp') }}"
                        alt=""
                        loading="lazy"
                        class="pointer-events-none h-22 transition duration-300 ease-in-out will-change-transform select-none group-hover:-translate-x-0.5"
                    />
                    {{-- Shiny circle --}}
                    <div
                        class="absolute -top-40 -right-40 -z-10 size-80 rounded-full bg-gradient-to-t from-white/5 to-white/50 dark:from-transparent dark:to-orange-500/65 dark:blur-2xl"
                    ></div>
                </div>

                {{-- Description --}}
                <div class="flex shrink-0 flex-col gap-4">
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/gold_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            1st Place
                        </h6>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/silver_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            2nd Place
                        </h6>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <img
                            src="{{ Vite::asset('resources/images/prizes/bronze_medal.webp') }}"
                            alt=""
                            loading="lazy"
                            class="h-8 lg:h-9"
                        />
                        {{-- Title --}}
                        <h6 class="text-lg font-medium lg:text-xl">
                            3rd Place
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How to enter --}}
    <section
        id="how-to-enter"
        class="mx-auto mt-20 max-w-5xl scroll-mt-32 px-5"
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
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="rounded-2xl bg-[#F4F1EE] p-12 dark:bg-[#1a1a2e]"
        >
            {{-- Header --}}
            <header class="text-center">
                <h2 class="text-4xl font-bold sm:text-5xl">How to enter</h2>
            </header>

            {{-- List --}}
            <div class="mt-8 space-y-4">
                <a
                    href="/newsletter"
                    target="_blank"
                    class="group dark:bg-mirage flex items-center justify-between gap-5 rounded-2xl bg-white/50 p-6 transition duration-300 hover:bg-white/80 dark:hover:bg-slate-700/40"
                >
                    {{-- Left side --}}
                    <div
                        class="flex flex-col items-start gap-x-2 gap-y-1 sm:flex-row sm:items-center"
                    >
                        <div
                            class="font-medium text-nowrap opacity-40 transition duration-300 group-hover:text-indigo-500 group-hover:opacity-100"
                        >
                            Step 1:
                        </div>
                        <h3 class="font-medium opacity-90">
                            Subscribe to our giveaway newsletter
                        </h3>
                    </div>

                    {{-- Icon --}}
                    <x-icons.right-arrow
                        class="size-3.5 shrink-0 transition duration-300 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>
                <a
                    href="https://x.com/nativephp"
                    target="_blank"
                    class="group dark:bg-mirage flex items-center justify-between gap-5 rounded-2xl bg-white/50 p-6 transition duration-300 hover:bg-white/80 dark:hover:bg-slate-700/40"
                >
                    {{-- Left side --}}
                    <div
                        class="flex flex-col items-start gap-x-2 gap-y-1 sm:flex-row sm:items-center"
                    >
                        <div
                            class="font-medium text-nowrap opacity-40 transition duration-300 group-hover:text-indigo-500 group-hover:opacity-100"
                        >
                            Step 2:
                        </div>
                        <h3 class="font-medium opacity-90">
                            Repost the Ticket Giveaway Announcement on X
                        </h3>
                    </div>

                    {{-- Icon --}}
                    <x-icons.right-arrow
                        class="size-3.5 shrink-0 transition duration-300 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>
                <a
                    href="https://bsky.app/profile/nativephp.com"
                    target="_blank"
                    class="group dark:bg-mirage flex items-center justify-between gap-5 rounded-2xl bg-white/50 p-6 transition duration-300 hover:bg-white/80 dark:hover:bg-slate-700/40"
                >
                    {{-- Left side --}}
                    <div
                        class="flex flex-col items-start gap-x-2 gap-y-1 sm:flex-row sm:items-center"
                    >
                        <div
                            class="font-medium text-nowrap opacity-40 transition duration-300 group-hover:text-indigo-500 group-hover:opacity-100"
                        >
                            Step 3:
                        </div>
                        <h3 class="font-medium opacity-90">
                            Repost the Ticket Giveaway Announcement on Bluesky
                        </h3>
                    </div>

                    {{-- Icon --}}
                    <x-icons.right-arrow
                        class="size-3.5 shrink-0 transition duration-300 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>
                <a
                    href="https://youtube.com/@NativePHPOfficial"
                    target="_blank"
                    class="group dark:bg-mirage flex items-center justify-between gap-5 rounded-2xl bg-white/50 p-6 transition duration-300 hover:bg-white/80 dark:hover:bg-slate-700/40"
                >
                    {{-- Left side --}}
                    <div
                        class="flex flex-col items-start gap-x-2 gap-y-1 sm:flex-row sm:items-center"
                    >
                        <div
                            class="font-medium text-nowrap opacity-40 transition duration-300 group-hover:text-indigo-500 group-hover:opacity-100"
                        >
                            Step 4:
                        </div>
                        <h3 class="font-medium opacity-90">
                            Subscribe to NativePHP on YouTube
                        </h3>
                    </div>

                    {{-- Icon --}}
                    <x-icons.right-arrow
                        class="size-3.5 shrink-0 transition duration-300 group-hover:translate-x-1"
                        aria-hidden="true"
                    />
                </a>
            </div>
        </div>
    </section>

    {{-- Legal --}}
    <section class="mx-auto mt-20 max-w-4xl px-5">
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
        >
            <h3 class="text-3xl font-semibold">Rules</h3>
            <article class="mt-4 text-gray-600 dark:text-gray-400">
                <ul class="list-disc space-y-2">
                    <li>
                        To enter, you must subscribe to our giveaway newsletter
                        list (step 1 above). Even if you are already subscribed
                        to the NativePHP newsletter, you must still subscribe to
                        the giveaway-specific newsletter list via the link
                        above. Steps 2 & 3 are optional but appreciated. No
                        purchase is necessary to enter.
                    </li>
                    <li>Only one entry is permitted per person.</li>
                    <li>Must be 18 years or older to enter.</li>
                    <li>
                        This giveaway is open until July 1st, 2025 at 12:00AM
                        UTC and winners will be drawn within 24 hours of the
                        giveaway closing.
                    </li>
                    <li>
                        The winners will be selected by randomly drawing names
                        from the list of giveaway newsletter subscribers.
                        Winners will be notified via email and must respond
                        within 48 hours to claim their prize. If a winner does
                        not respond within 48 hours, another winner will be
                        selected in their place.
                    </li>
                    <li>
                        Winners are responsible for adhering to applicable laws,
                        regulations, and taxes within their jurisdiction.
                    </li>
                    <li>
                        The approximate prize values (in USD) are as follows:
                        1st Place: $880, 2nd Place: $130, 3rd Place: $30.
                    </li>
                    <li>
                        This giveaway is provided by Bifrost Technology, LLC
                        located at 1111B S Governors Ave STE 2838, Dover, DE
                        19904. The giveaway is not affiliated with or endorsed
                        by Laracon US, Twitter, or any other entity. By
                        participating, you agree to the terms and conditions
                        outlined in these official rules.
                    </li>
                </ul>
            </article>
        </div>
    </section>
</x-layout>
