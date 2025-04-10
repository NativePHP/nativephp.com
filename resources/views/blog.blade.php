<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mt-10 px-5 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-0 grid place-items-center text-center">
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
                class="text-3xl font-extrabold sm:text-4xl"
            >
                Blog
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
                class="mx-auto max-w-2xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Welcome to our blog! Here, we share insights, updates, and
                stories from our community. Stay tuned for the latest news and
                articles.
            </h2>

            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute -top-1/2 right-0 -z-20 h-60 w-60 rounded-full bg-emerald-200/35 blur-[100px] md:right-1/2 md:w-80 dark:bg-emerald-500/20"
                aria-hidden="true"
            ></div>

            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute left-0 top-1/2 -z-30 h-60 w-60 rounded-full bg-amber-100/70 blur-[100px] md:left-1/2 md:w-80 dark:bg-violet-500/20"
                aria-hidden="true"
            ></div>
        </header>
    </section>

    {{-- Articles --}}
    <section class="relative z-10 mt-10 px-5">
        {{-- List --}}
        <div class="mx-auto w-full max-w-xl">
            <a
                href="#"
                class="group block rounded-2xl bg-gray-200/40 p-7 transition duration-300 hover:bg-emerald-100/50 dark:bg-mirage/50 dark:hover:bg-emerald-500/10"
            >
                {{-- Header --}}
                <div class="flex items-start justify-between gap-10">
                    {{-- Title --}}
                    <h3
                        class="line-clamp-4 max-w-xs text-xl font-semibold leading-relaxed"
                    >
                        NativePHP for desktop v1 is finally here!
                    </h3>

                    {{-- Arrow --}}
                    <x-icons.right-arrow
                        class="-ml-5 mt-2 size-3.5 shrink-0 transition duration-300 will-change-transform group-hover:translate-x-1"
                    />
                </div>

                <div class="flex items-end justify-between gap-10 pt-5">
                    {{-- Date --}}
                    <div class="shrink-0 text-sm opacity-50">April 9, 2025</div>

                    {{-- Content --}}
                    <p
                        class="line-clamp-3 max-w-72 text-xs leading-relaxed opacity-80"
                    >
                        🎉 WE DID IT! We finally got to v1. I almost don't
                        believe it! This is an awesome milestone. For a project
                        that started as just an idea, to see it reach a truly
                        stable place and support building powerful apps across
                        all major platforms is just incredible.
                    </p>
                </div>
            </a>
        </div>
    </section>
</x-layout>
