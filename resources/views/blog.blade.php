<x-layout title="Blog">
    <section class="mx-auto mt-10 w-full max-w-3xl">
        {{-- Hero --}}
        <header class="relative z-0 grid place-items-center text-center">
            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                {
                                    autoAlpha: 0,
                                    y: -10,
                                },
                                {
                                    autoAlpha: 1,
                                    y: 0,
                                    duration: 0.7,
                                    ease: 'power1.out',
                                },
                            )
                        })
                    }
                "
                class="text-3xl font-bold sm:text-4xl"
            >
                Blog
            </h1>
            {{-- Introduction Description --}}
            <h2
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                {
                                    autoAlpha: 0,
                                    y: 10,
                                },
                                {
                                    autoAlpha: 1,
                                    y: 0,
                                    duration: 0.7,
                                    ease: 'power1.out',
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
        </header>

        {{-- Divider --}}
        <x-divider />

        {{-- Articles --}}
        <section
            class="relative z-10 mt-2"
            aria-labelledby="blog-articles-heading"
        >
            {{-- Semantic heading for section (visually hidden) --}}
            <h2
                id="blog-articles-heading"
                class="sr-only"
            >
                Blog Articles
            </h2>
            {{-- Main --}}
            <div class="flex items-start gap-5">
                {{-- List --}}
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                gsap.fromTo(
                                    $el.children,
                                    {
                                        x: -50,
                                        autoAlpha: 0,
                                    },
                                    {
                                        x: 0,
                                        autoAlpha: 1,
                                        duration: 0.7,
                                        ease: 'circ.out',
                                        stagger: 0.1,
                                    },
                                )
                            })
                        }
                    "
                    class="flex grow flex-col gap-5"
                >
                    @foreach ($articles as $article)
                        <x-blog.article-card
                            :title="$article->title"
                            :url="route('article', $article)"
                            :date="$article->published_at"
                        >
                            {{ $article->excerpt }}
                        </x-blog.article-card>
                    @endforeach
                </div>

                {{-- Sidebar --}}
                <x-blog.sidebar />
            </div>

            {{-- Mobile partner card --}}
            <div class="mt-5 min-[850px]:hidden">
                <x-sponsors.lists.docs.featured-sponsors />
                <a href="/partners" class="mt-3 block text-center text-xs text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Become a Partner</a>
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
                                gsap.fromTo(
                                    $el,
                                    {
                                        autoAlpha: 0,
                                        x: -10,
                                    },
                                    {
                                        autoAlpha: 1,
                                        x: 0,
                                        duration: 0.7,
                                        ease: 'power1.out',
                                    },
                                )
                            })
                        }
                    "
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
                                gsap.fromTo(
                                    $el,
                                    {
                                        autoAlpha: 0,
                                        x: 10,
                                    },
                                    {
                                        autoAlpha: 1,
                                        x: 0,
                                        duration: 0.7,
                                        ease: 'power1.out',
                                    },
                                )
                            })
                        }
                    "
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
    </section>
</x-layout>
