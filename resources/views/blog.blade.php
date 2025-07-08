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
                class="text-3xl font-extrabold will-change-transform sm:text-4xl"
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
                class="mx-auto max-w-2xl pt-4 text-base/relaxed text-gray-600 will-change-transform sm:text-lg/relaxed dark:text-gray-400"
            >
                Welcome to our blog! Here, we share insights, updates, and
                stories from our community. Stay tuned for the latest news and
                articles.
            </h2>

            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute -top-1/2 right-0 -z-20 h-60 w-60 rounded-full bg-violet-300/60 blur-[150px] md:right-1/2 md:w-80 dark:bg-violet-500/20"
                aria-hidden="true"
            ></div>

            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-1/2 left-0 -z-30 h-60 w-60 rounded-full bg-orange-200/60 blur-[150px] md:left-1/2 md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>
        </header>
    </section>

    {{-- Articles --}}
    <section
        class="relative z-10 mx-auto mt-10 w-full max-w-xl px-5"
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
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                x: [-50, 0],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
            class="flex flex-col gap-5"
        >
            @foreach ($articles as $article)
                <x-article-card
                    :title="$article->title"
                    :url="route('article', $article)"
                    :date="$article->published_at->format('Y-m-d')"
                >
                    {{ $article->excerpt }}
                </x-article-card>
            @endforeach
        </div>

        {{-- Pagination --}}
        <nav
            class="flex items-center justify-between gap-5 pt-2.5"
            aria-label="Blog pagination"
        >
            {{-- Previous --}}
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
                class="will-change-transform"
            >
                @if (! $articles->onFirstPage())
                    <a
                        href="{{ $articles->previousPageUrl() }}"
                        class="inline-block p-1.5 opacity-60 transition duration-200 hover:opacity-100"
                        aria-label="Go to previous page"
                        rel="prev"
                    >
                        <span class="sr-only">Navigate to</span>
                        Previous
                    </a>
                @endif
            </div>

            {{-- Next --}}
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
                class="will-change-transform"
            >
                @if (! $articles->onLastPage())
                    <a
                        href="{{ $articles->nextPageUrl() }}"
                        class="inline-block p-1.5 opacity-80 transition duration-200 hover:opacity-100"
                        aria-label="Go to next page"
                        rel="next"
                    >
                        <span class="sr-only">Navigate to</span>
                        Next
                    </a>
                @endif
            </div>
        </nav>
    </section>
</x-layout>
