<x-layout title="NativePHP brand assets">
    {{-- Hero Section --}}
    <section
        class="relative mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        {{-- Decorative dashed grid background (pure CSS) --}}
        <style>
            .brand-hero-grid {
                /* Grid appearance */
                --grid-line: rgba(0, 0, 0, 0.1);
                /* grid cell size (responsive below) */
                --cell: 64px;
                /* clean single-line grid to avoid seams */
                background-image:
                    linear-gradient(
                        to bottom,
                        var(--grid-line) 1px,
                        transparent 1px
                    ),
                    linear-gradient(
                        to right,
                        var(--grid-line) 1px,
                        transparent 1px
                    );
                background-size: var(--cell) var(--cell);
            }
            .dark .brand-hero-grid {
                --grid-line: rgba(255, 255, 255, 0.1);
            }
        </style>

        {{-- Grid --}}
        <div
            aria-hidden="true"
            role="presentation"
            class="brand-hero-grid pointer-events-none absolute -top-5 left-1/2 z-0 h-[220px] w-[min(92vw,66rem)] -translate-x-1/2 mask-radial-from-50% mask-radial-farthest-side mask-radial-at-top sm:h-[280px] lg:h-[340px]"
        ></div>
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
                class="text-3xl font-bold sm:text-4xl"
            >
                NativePHP brand assets
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
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Discover the brand assets for NativePHP, including logos, color
                palettes, and typography guidelines.
            </h2>
        </header>
    </section>
</x-layout>
