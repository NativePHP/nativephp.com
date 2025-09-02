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
        <div
            x-init="
                () => {
                    motion.inView($el, () => {
                        gsap.fromTo(
                            $el,
                            { autoAlpha: 0, x: 5 },
                            { autoAlpha: 1, x: 0, duration: 0.7, ease: 'power1.out' },
                        )
                    })
                }
            "
            class="flex items-center pt-3.5 pb-3"
            aria-hidden="true"
        >
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
            <div class="h-0.5 w-full bg-gray-200/90 dark:bg-[#242734]"></div>
            <div
                class="size-1.5 rotate-45 bg-gray-200/90 dark:bg-[#242734]"
            ></div>
        </div>

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
                        <x-article-card
                            :title="$article->title"
                            :url="route('article', $article)"
                            :date="$article->published_at"
                        >
                            {{ $article->excerpt }}
                        </x-article-card>
                    @endforeach
                </div>
                {{-- Sidebar --}}
                <div
                    x-init="
                        () => {
                            motion.inView($el, () => {
                                gsap.fromTo(
                                    $el,
                                    { autoAlpha: 0, x: 5 },
                                    { autoAlpha: 1, x: 0, duration: 0.7, ease: 'power1.out' },
                                )
                            })
                        }
                    "
                    class="sticky top-20 right-0 hidden max-w-52 shrink-0 min-[850px]:block"
                >
                    {{-- Sponsors --}}
                    <h3 class="flex items-center gap-1.5 opacity-60">
                        {{-- Icon --}}
                        <x-icons.star-circle class="size-6" />
                        {{-- Label --}}
                        <div>Sponsors</div>
                    </h3>
                    {{-- List --}}
                    <div class="space-y-3 pt-2.5">
                        <x-sponsors.lists.docs.featured-sponsors />
                    </div>
                    {{-- List --}}
                    <div class="space-y-3 pt-2.5">
                        <x-sponsors.lists.docs.corporate-sponsors />
                    </div>
                </div>
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
