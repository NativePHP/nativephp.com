<x-layout title="Blog">
    {{-- Hero --}}
    <section
        class="mx-auto mt-10 w-full max-w-3xl md:mt-14"
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
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [5, 0],
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
                <a
                    href="{{ route('blog') }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 will-change-transform hover:-translate-x-0.5 hover:opacity-100"
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
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-5, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-8 text-3xl font-extrabold will-change-transform sm:text-4xl"
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
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [5, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="flex items-center pt-3.5 pb-3 will-change-transform"
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

        {{-- Content --}}
        <article
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
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
            class="prose dark:prose-headings:text-white mt-2 max-w-none text-gray-600 will-change-transform dark:text-gray-400"
            aria-labelledby="article-title"
        >
            {!! App\Support\CommonMark\CommonMark::convertToHtml($article->content) !!}
        </article>
    </section>
</x-layout>
