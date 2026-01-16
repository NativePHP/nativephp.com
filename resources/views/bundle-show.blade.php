<x-layout :title="$bundle->name . ' - Plugin Bundle'">
    <section
        class="mx-auto mt-10 w-full max-w-5xl"
        aria-labelledby="bundle-title"
    >
        <header class="relative">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-amber-500/30"
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
                    href="{{ route('plugins.directory') }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 hover:-translate-x-0.5 hover:opacity-100"
                    aria-label="Return to plugin directory"
                >
                    <x-icons.right-arrow
                        class="size-3 shrink-0 -scale-x-100"
                        aria-hidden="true"
                    />
                    <div class="text-sm">Plugin Directory</div>
                </a>
            </div>

            {{-- Bundle icon and title --}}
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
                class="mt-8 flex items-center gap-4"
            >
                @if ($bundle->hasLogo())
                    <img
                        src="{{ $bundle->getLogoUrl() }}"
                        alt="{{ $bundle->name }} logo"
                        class="size-20 shrink-0 rounded-2xl object-cover"
                    />
                @else
                    <div class="grid size-20 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>
                    </div>
                @endif
                <div>
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                        Bundle
                    </span>
                    <h1
                        id="bundle-title"
                        class="mt-2 text-2xl font-bold sm:text-3xl"
                    >
                        {{ $bundle->name }}
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ $bundle->plugins->count() }} plugins included
                    </p>
                </div>
            </div>
        </header>

        {{-- Divider --}}
        <x-divider />

        <div class="mt-2 flex flex-col-reverse gap-8 lg:flex-row lg:items-start">
            {{-- Main content - Description and Plugins --}}
            <div class="grow">
                {{-- Description --}}
                @if ($bundle->description)
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
                        class="mb-8"
                    >
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">About this Bundle</h2>
                        <p class="mt-4 text-gray-600 dark:text-gray-400">{{ $bundle->description }}</p>
                    </div>
                @endif

                {{-- Included Plugins --}}
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
                >
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Included Plugins
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Purchase this bundle to get access to all {{ $bundle->plugins->count() }} plugins.
                    </p>
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        @foreach ($bundle->plugins as $plugin)
                            <x-plugin-card :plugin="$plugin" />
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sidebar - Purchase Box --}}
            <aside
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
                class="w-full shrink-0 lg:sticky lg:top-24 lg:w-72"
            >
                <div class="rounded-2xl border-2 border-amber-500 bg-gradient-to-br from-amber-50 to-orange-50 p-6 dark:border-amber-400 dark:from-amber-950/50 dark:to-orange-950/50">
                    <div class="text-center">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Bundle Price</p>
                        <p class="mt-1 text-4xl font-bold text-gray-900 dark:text-white">
                            {{ $bundle->formatted_price }}
                        </p>
                        @if ($bundle->discount_percent > 0)
                            <div class="mt-2">
                                <span class="text-lg text-gray-500 line-through dark:text-gray-400">
                                    {{ $bundle->formatted_retail_value }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm font-medium text-green-600 dark:text-green-400">
                                Save {{ $bundle->formatted_savings }} ({{ $bundle->discount_percent }}% off)
                            </p>
                        @endif
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">One-time purchase</p>
                    </div>

                    <div class="mt-6">
                        @auth
                            @if ($bundle->isOwnedBy(auth()->user()))
                                <button
                                    type="button"
                                    disabled
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-gray-300 px-4 py-3 text-sm font-semibold text-gray-500 dark:bg-gray-600 dark:text-gray-400"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    You own all plugins
                                </button>
                            @else
                                <form action="{{ route('cart.bundle.add', $bundle) }}" method="POST">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/25 transition hover:bg-amber-700 hover:shadow-amber-500/40 dark:shadow-amber-500/10"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                        </svg>
                                        Add Bundle to Cart
                                    </button>
                                </form>
                            @endif
                        @else
                            <a
                                href="{{ route('customer.login', ['return' => route('bundles.show', $bundle)]) }}"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-500/25 transition hover:bg-amber-700 hover:shadow-amber-500/40 dark:shadow-amber-500/10"
                            >
                                Log in to Purchase
                            </a>
                        @endauth
                    </div>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
