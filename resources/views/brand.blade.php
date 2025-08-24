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

        {{-- Header --}}
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
            <p
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
                class="mx-auto max-w-3xl pt-4 text-base/relaxed text-pretty text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                This page provides assets and rules for using
                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    NativePHP
                </span>
                visuals in articles, videos, open source projects, and community
                content.

                <br />
                <br />

                Our name and logo are trademarks of

                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    Bifrost Technology.
                </span>
                Please use them respectfully and only in ways that reflect
                <span class="font-medium text-gray-700 dark:text-zinc-300">
                    NativePHP
                </span>
                accurately.
            </p>
        </header>

        {{-- List --}}
        <div
            class="mx-auto mt-10 flex w-full max-w-md flex-col items-stretch gap-5"
        >
            {{-- Primary logo --}}
            <div
                x-data="{ isHovered: false }"
                class="flex flex-col items-center gap-5"
            >
                {{-- Asset --}}
                <div
                    class="grid h-50 w-full place-items-center rounded-xl bg-gray-100 p-5 ring-1 ring-gray-300"
                >
                    <img
                        src="/brand-assets/logo/nativephp.svg"
                        alt=""
                        loading="lazy"
                        class="h-8 transition duration-200 will-change-transform"
                        x-bind:class="{
                            'scale-105': isHovered,
                        }"
                    />
                </div>

                {{-- Download button --}}
                <a
                    download
                    href="/brand-assets/logo/nativephp.svg"
                    class="inline-flex items-center gap-2 rounded-xl bg-white py-3 pr-5 pl-3.5 text-sm font-medium ring-1 ring-gray-300 transition duration-200 ring-inset hover:bg-gray-100 dark:bg-cloud/60 dark:text-white dark:ring-transparent dark:hover:bg-cloud"
                    x-on:mouseenter="isHovered = true"
                    x-on:mouseleave="isHovered = false"
                >
                    <x-icons.download class="size-5" />
                    <div>Download</div>
                </a>
            </div>
        </div>
    </section>
</x-layout>
