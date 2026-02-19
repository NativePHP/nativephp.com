<x-layout title="{{ $article->title }} - Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-5xl"
        aria-labelledby="article-title"
    >
        <header class="relative grid place-items-center text-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>

            {{-- Back button --}}
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
            >
                <a
                    href="{{ route('blog') }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 hover:-translate-x-0.5 hover:opacity-100"
                    aria-label="Return to blog listing"
                >
                    <x-icons.right-arrow
                        class="size-3 shrink-0 -scale-x-100"
                        aria-hidden="true"
                    />
                    <div class="text-sm">Blog</div>
                </a>
            </div>

            {{-- Primary Heading --}}
            <h1
                id="article-title"
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, x: -5 },
                                { autoAlpha: 1, x: 0, duration: 0.7, ease: 'power1.out' },
                            )
                        })
                    }
                "
                class="mt-8 text-3xl font-bold sm:text-4xl"
            >
                {{ $article->title }}
            </h1>

            {{-- Date --}}
            <div
                class="inline-flex items-center gap-1.5 pt-4 opacity-60"
                aria-label="Publication date"
            >
                <x-icons.date
                    class="size-5 shrink-0"
                    aria-hidden="true"
                />
                <time
                    datetime="2025-04-09"
                    class="text-sm"
                >
                    {{ $article->published_at?->format('F j, Y') }}
                </time>
            </div>
        </header>

        {{-- Divider --}}
        <x-divider />

        <div class="mt-2 flex items-start gap-5">
            {{-- Content --}}
            <article
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 5 },
                                { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power1.out' },
                            )
                        })
                    }
                "
                class="prose max-w-none grow text-gray-600 dark:text-gray-400 dark:prose-headings:text-white"
                aria-labelledby="article-title"
            >
                {!! App\Support\CommonMark\CommonMark::convertToHtml($article->content) !!}
            </article>

            {{-- Sidebar --}}
            <x-blog.sidebar />
        </div>

        {{-- Mobile partner card --}}
        <div class="mt-5 min-[850px]:hidden">
            <x-sponsors.lists.docs.featured-sponsors />
            <a href="/partners" class="mt-3 block text-center text-xs text-gray-500 transition hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">Become a Partner</a>
        </div>
    </section>
</x-layout>
