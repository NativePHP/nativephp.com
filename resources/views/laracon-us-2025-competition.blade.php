<x-layout>
    {{-- Hero Section --}}
    <section class="mx-auto mt-12 max-w-5xl px-5">
        <div class="grid place-items-center text-center">
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
                class="text-lg font-medium text-gray-600 dark:text-gray-400"
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
                class="mt-2 grid grid-cols-4 gap-10 text-5xl"
            >
                <div class="flex flex-col items-center">
                    <number-flow
                        x-ref="dd"
                        class="font-bold"
                    ></number-flow>
                    <div
                        class="text-base text-slate-600 uppercase dark:text-white/60"
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
                        class="text-base text-slate-600 uppercase dark:text-white/60"
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
                        class="text-base text-slate-600 uppercase dark:text-white/60"
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
                        class="text-base text-slate-600 uppercase dark:text-white/60"
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
                class="relative isolate mt-8"
            >
                <img
                    src="{{ Vite::asset('resources/images/laraconus2025ticket.webp') }}"
                    alt="Laracon US 2025 Ticket"
                    class="max-w-130"
                />
                <div
                    class="absolute top-1/2 right-1/2 -z-10 hidden h-full w-full translate-x-1/2 -translate-y-1/2 rounded-full bg-slate-500/25 blur-3xl dark:block"
                ></div>
            </div>

            <header
                class="relative z-10 mt-10 grid place-items-center text-center"
            >
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
                    class="mt-5 w-full max-w-56"
                >
                    <a
                        href="mailto:partners@nativephp.com?subject=Interested%20In%20Being%20a%20Partner"
                        class="flex items-center justify-center gap-2.5 rounded-2xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-violet-400/80 dark:drop-shadow-xl dark:drop-shadow-transparent dark:hover:bg-violet-400 dark:hover:drop-shadow-violet-400/30"
                    >
                        Enter to Win
                    </a>
                </div>
            </header>
        </div>
    </section>
</x-layout>
