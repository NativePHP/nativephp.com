@props([
    'title',
])

{{-- These pages are the tail end of a signup flow; they have nothing to index. --}}
@push('head')
    <meta
        name="robots"
        content="noindex, nofollow"
    />
@endpush

<x-layout :title="$title">
    <section
        class="mx-auto flex w-full max-w-2xl flex-col items-center px-5 py-24 text-center md:py-32"
        aria-labelledby="newsletter-status-heading"
    >
        {{-- Blurred circle - Decorative --}}
        <div
            class="absolute top-40 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-emerald-500/40"
            aria-hidden="true"
        ></div>

        <div
            x-init="
                () => {
                    motion.animate(
                        $el,
                        {
                            opacity: [0, 1],
                            y: [10, 0],
                        },
                        {
                            duration: 0.7,
                            ease: motion.circOut,
                        },
                    )
                }
            "
            class="flex flex-col items-center opacity-0"
        >
            {{-- Icon --}}
            <div
                class="grid size-16 place-items-center rounded-2xl bg-emerald-50 ring-1 ring-black/5 dark:bg-emerald-950/50 dark:ring-white/10"
            >
                {{ $icon }}
            </div>

            {{-- Heading --}}
            <h1
                id="newsletter-status-heading"
                class="mt-8 text-3xl font-extrabold text-balance sm:text-4xl"
            >
                {{ $title }}
            </h1>

            {{-- Message --}}
            <div
                class="mt-4 max-w-lg leading-relaxed text-pretty opacity-70 [&_p+p]:mt-3"
            >
                {{ $slot }}
            </div>

            {{-- Actions --}}
            <div
                class="mt-10 flex flex-col flex-wrap items-center gap-3 sm:flex-row sm:justify-center"
            >
                {{ $actions ?? '' }}

                <a
                    href="{{ route('welcome') }}"
                    class="rounded-xl px-6 py-3 text-sm font-semibold ring-1 ring-black/10 transition duration-200 hover:bg-gray-50 dark:ring-white/15 dark:hover:bg-white/5"
                >
                    Back to nativephp.com
                </a>
            </div>
        </div>
    </section>
</x-layout>
