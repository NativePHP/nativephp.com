<x-layout :title="$plugin->name . ' - License'">
    <section
        class="mx-auto mt-10 w-full max-w-7xl"
        aria-labelledby="license-title"
    >
        <header class="relative">
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
                    href="{{ route('plugins.show', $plugin->routeParams()) }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 hover:-translate-x-0.5 hover:opacity-100"
                    aria-label="Return to plugin details"
                >
                    <x-icons.right-arrow
                        class="size-3 shrink-0 -scale-x-100"
                        aria-hidden="true"
                    />
                    <div class="text-sm">Back to {{ $plugin->name }}</div>
                </a>
            </div>

            {{-- Title --}}
            <div
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
                class="mt-8"
            >
                <h1
                    id="license-title"
                    class="text-2xl font-bold sm:text-3xl"
                >
                    License
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    {{ $plugin->name }} &middot; {{ $plugin->getLicense() ?? 'License' }}
                </p>
            </div>
        </header>

        {{-- Divider --}}
        <x-divider />

        {{-- License Content --}}
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
            class="prose prose-gray mt-8 max-w-none dark:prose-invert"
        >
            {!! $plugin->license_html !!}
        </article>
    </section>
</x-layout>
