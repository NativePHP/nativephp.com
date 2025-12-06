<x-layout title="{{ $platform ? ucfirst($platform) . ' ' : '' }}Showcase - Apps Built with NativePHP">
    {{-- Hero Section --}}
    <section
        class="mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 size-60 translate-x-1/2 rounded-full bg-blue-100/70 blur-3xl md:w-80 dark:bg-slate-500/30"
                aria-hidden="true"
            ></div>

            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: -10 },
                                { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="font-bold text-center"
            >
                <div class="relative">
                    <div
                        class="bg-gradient-to-br from-zinc-900 to-zinc-500 bg-clip-text text-5xl tracking-tighter text-transparent sm:text-6xl dark:from-white"
                    >
                        @if($platform === 'mobile')
                            Mobile
                        @elseif($platform === 'desktop')
                            Desktop
                        @else
                            App
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-center gap-2">
                    <div
                        class="bg-gradient-to-br from-blue-600 to-cyan-400 bg-clip-text text-5xl tracking-tighter text-transparent sm:text-6xl dark:bg-gradient-to-t"
                    >
                        Showcase
                    </div>
                </div>
            </h1>

            <div class="mt-5 flex items-center justify-center gap-1">
                <div class="size-2.5 rounded-sm bg-gradient-to-tl from-blue-400/70 to-blue-300/70"></div>
                <div class="size-2.5 rounded-sm bg-gradient-to-tl from-cyan-400/70 to-cyan-300/70"></div>
                <div class="size-2.5 rounded-sm bg-gradient-to-tl from-teal-400/70 to-teal-300/70"></div>
                <div class="size-2.5 rounded-sm bg-gradient-to-tl from-emerald-400/70 to-emerald-300/70"></div>
            </div>

            {{-- Description --}}
            <p
                x-init="
                    () => {
                        motion.inView($el, () => {
                            gsap.fromTo(
                                $el,
                                { autoAlpha: 0, y: 10 },
                                { autoAlpha: 1, y: 0, duration: 0.7, ease: 'power2.out' },
                            )
                        })
                    }
                "
                class="mx-auto mt-5 max-w-2xl text-center text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Discover amazing {{ $platform ?? '' }} apps built by the NativePHP community. From productivity tools to creative applications, see what's possible with NativePHP.
            </p>

            {{-- Platform Filter --}}
            <div class="mt-8 flex items-center justify-center gap-2">
                <a
                    href="{{ route('showcase') }}"
                    @class([
                        'px-4 py-2 rounded-full text-sm font-medium transition-all',
                        'bg-gray-900 text-white dark:bg-white dark:text-gray-900' => !$platform,
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' => $platform,
                    ])
                >
                    All Apps
                </a>
                <a
                    href="{{ route('showcase', 'mobile') }}"
                    @class([
                        'px-4 py-2 rounded-full text-sm font-medium transition-all',
                        'bg-gray-900 text-white dark:bg-white dark:text-gray-900' => $platform === 'mobile',
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' => $platform !== 'mobile',
                    ])
                >
                    Mobile
                </a>
                <a
                    href="{{ route('showcase', 'desktop') }}"
                    @class([
                        'px-4 py-2 rounded-full text-sm font-medium transition-all',
                        'bg-gray-900 text-white dark:bg-white dark:text-gray-900' => $platform === 'desktop',
                        'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' => $platform !== 'desktop',
                    ])
                >
                    Desktop
                </a>
            </div>
        </header>
    </section>

    {{-- Showcase Grid --}}
    <section class="relative z-10 mt-12 mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        @if ($showcases->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($showcases as $showcase)
                    <x-showcase-card :showcase="$showcase" />
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($showcases->hasPages())
                <div class="mt-12">
                    {{ $showcases->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div
                    class="mx-auto max-w-md rounded-2xl border border-gray-200 bg-white p-8 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/50"
                >
                    <div class="mb-4 text-6xl">🚀</div>
                    <h3
                        class="mb-2 text-xl font-semibold text-gray-900 dark:text-white"
                    >
                        No Apps Yet
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if($platform)
                            No {{ $platform }} apps have been showcased yet. Be the first to submit yours!
                        @else
                            The showcase is empty. Be the first to submit your NativePHP app!
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </section>

    {{-- CTA Section --}}
    <section class="relative z-10 mt-16 mb-12 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-2xl bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-gray-800 dark:to-gray-900 p-8 sm:p-12 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                Built something with NativePHP?
            </h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400 max-w-xl mx-auto">
                We'd love to feature your app in our showcase. Share your creation with the NativePHP community!
            </p>
            @auth
                <a
                    href="{{ route('customer.showcase.create') }}"
                    class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                >
                    Submit Your App
                </a>
            @else
                <a
                    href="{{ route('customer.login') }}"
                    class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 transition-colors"
                >
                    Log in to Submit
                </a>
            @endauth
        </div>
    </section>
</x-layout>
