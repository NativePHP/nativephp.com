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
    <section
        class="relative z-10 mt-10 px-5"
        aria-labelledby="blog-articles-heading"
    >
        {{-- Semantic heading for section (visually hidden) --}}
        <h2
            id="blog-articles-heading"
            class="sr-only"
        >
            Blog Articles
        </h2>

        {{-- List --}}
        <div class="mx-auto flex w-full max-w-xl flex-col gap-5">
            <x-article-card
                title="NativePHP for desktop v1 is finally here!"
                url="#"
                date="2025-04-09"
            >
                🎉 WE DID IT! We finally got to v1. I almost don't believe it!
                This is an awesome milestone. For a project that started as just
                an idea, to see it reach a truly stable place and support
                building powerful apps across all major platforms is just
                incredible.
            </x-article-card>
            <x-article-card
                title="Dropping Laravel 10 support"
                url="#"
                date="2025-04-4"
            >
                Hey team, this is just a quick note about Laravel version
                support. Per our Support Policy matrix, we will be dropping
                Laravel 10 support for NativePHP for desktop v1. Laravel 10
                reached end of life back in February 2025.
            </x-article-card>
            <x-article-card
                title="NativePHP for mobile—Pricing update!"
                url="#"
                date="2025-03-27"
            >
                Earlier this week I spoke at the Laravel Worldwide Meetup where
                I unveiled: 🌐 A brand new nativephp.com, lovingly (and
                painstakingly!) crafted by the incredible
                @HassanZahirnia
            </x-article-card>

            {{-- Pagination --}}
            <nav
                class="-mt-2 flex items-center justify-between gap-5"
                aria-label="Blog pagination"
            >
                {{-- Previous --}}
                <a
                    href="#"
                    class="inline-block p-1.5 opacity-50 transition duration-200 hover:opacity-100"
                    aria-label="Go to previous page"
                    rel="prev"
                >
                    <span class="sr-only">Navigate to</span>
                    Previous
                </a>

                {{-- Next --}}
                <a
                    href="#"
                    class="inline-block p-1.5 opacity-80 transition duration-200 hover:opacity-100"
                    aria-label="Go to next page"
                    rel="next"
                >
                    <span class="sr-only">Navigate to</span>
                    Next
                </a>
            </nav>
        </div>
    </section>
</x-layout>
