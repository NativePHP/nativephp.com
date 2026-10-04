<x-layout :title="$partnerName">
    <div class="mx-auto max-w-4xl">
        {{-- Hero --}}
        <section class="mt-12">
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
                {{-- Partner Logo --}}
                @if (isset($logo))
                    <div class="flex justify-center">
                        {{ $logo }}
                    </div>
                @endif

                {{-- Tagline --}}
                <h1 class="mt-6 text-center text-3xl font-bold md:text-4xl">
                    {{ $tagline }}
                </h1>

                {{-- Introduction --}}
                @if (isset($introduction))
                    <div class="mx-auto mt-6 max-w-3xl text-center text-lg text-gray-600 dark:text-zinc-400">
                        {{ $introduction }}
                    </div>
                @endif
            </div>
        </section>

        {{-- Featured Project --}}
        @if (isset($featuredProject))
            <section class="mt-16">
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
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
                            })
                        }
                    "
                    class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-10"
                >
                    {{ $featuredProject }}
                </div>
            </section>
        @endif

        {{-- What We Build --}}
        @if (isset($whatWeBuild))
            <section class="mt-16">
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
                >
                    <h2 class="text-2xl font-semibold md:text-3xl">
                        What We Build
                    </h2>
                    <div class="mt-6 space-y-3 text-gray-600 dark:text-zinc-400">
                        {{ $whatWeBuild }}
                    </div>
                </div>
            </section>
        @endif

        {{-- Why Teams Work With Us --}}
        @if (isset($whyWorkWithUs))
            <section class="mt-16">
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
                    <h2 class="text-2xl font-semibold md:text-3xl">
                        Why Teams Work With Us
                    </h2>
                    <div class="mt-6 space-y-3 text-gray-600 dark:text-zinc-400">
                        {{ $whyWorkWithUs }}
                    </div>
                </div>
            </section>
        @endif

        {{-- Contact --}}
        @if (isset($contact))
            <section class="mt-16 pb-24">
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
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
                            })
                        }
                    "
                    class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-10"
                >
                    <h2 class="text-2xl font-semibold md:text-3xl">
                        Let's Talk
                    </h2>
                    <div class="mt-6 text-gray-600 dark:text-zinc-400">
                        {{ $contact }}
                    </div>
                </div>
            </section>
        @endif
    </div>
</x-layout>
