<x-layout>
    {{-- Hero Section --}}
    <section class="mx-auto mt-12 max-w-5xl px-5">
        <div
            x-ref="ticketEvent"
            class="grid place-items-center text-center"
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
                x-data="countdown('2025-06-31T23:59:59Z')"
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
                        href="#enter-to-win"
                        class="flex items-center justify-center gap-2.5 rounded-2xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-violet-400/80 dark:drop-shadow-xl dark:drop-shadow-transparent dark:hover:bg-violet-400 dark:hover:drop-shadow-violet-400/30"
                    >
                        Enter to Win
                    </a>
                </div>
            </header>
        </div>
    </section>

    {{-- Prizes --}}
    <section class="mx-auto mt-25 max-w-5xl px-5">
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
        <div class="mt-5 flex flex-wrap items-center justify-center gap-5">
            <div
                class="group relative isolate flex w-full max-w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-violet-50 to-violet-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
            >
                {{-- Title --}}
                <h5
                    class="text-2xl leading-relaxed font-semibold text-violet-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-violet-400"
                >
                    Laracon Ticket
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
            <div
                class="group relative isolate flex w-full max-w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-sky-50 to-sky-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
            >
                {{-- Title --}}
                <h5
                    class="text-2xl leading-relaxed font-semibold text-sky-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-sky-300"
                >
                    NativePHP T-Shirt
                </h5>

                {{-- Illustration --}}
                <img
                    src="{{ Vite::asset('resources/images/prizes/nativephp_black_shirt.webp') }}"
                    alt=""
                    loading="lazy"
                    class="pointer-events-none -mb-20 h-40 transition duration-300 ease-in-out will-change-transform select-none group-hover:-translate-x-0.5 dark:contrast-120"
                />

                {{-- Shiny circle --}}
                <div
                    class="absolute -top-40 -right-40 -z-10 size-80 rounded-full bg-gradient-to-t from-white/5 to-white/50 dark:from-transparent dark:to-sky-500/65 dark:blur-2xl"
                ></div>
            </div>
            <div
                class="group relative isolate flex w-full max-w-75 items-center gap-5 overflow-hidden rounded-2xl bg-gradient-to-br from-orange-50 to-orange-300 p-8 dark:bg-gradient-to-bl dark:from-white/10 dark:to-white/2"
            >
                {{-- Title --}}
                <h5
                    class="text-2xl leading-relaxed font-semibold text-orange-900 transition duration-300 ease-in-out will-change-transform group-hover:translate-x-0.5 dark:text-orange-400"
                >
                    NativePHP License
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
        </div>
    </section>
</x-layout>
