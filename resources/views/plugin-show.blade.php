<x-layout title="{{ $plugin->name }} - Plugin">
    <section
        class="mx-auto mt-10 w-full max-w-5xl"
        aria-labelledby="plugin-title"
    >
        <header class="relative">
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
                    href="{{ route('plugins.marketplace') }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 hover:-translate-x-0.5 hover:opacity-100"
                    aria-label="Return to plugin marketplace"
                >
                    <x-icons.right-arrow
                        class="size-3 shrink-0 -scale-x-100"
                        aria-hidden="true"
                    />
                    <div class="text-sm">Plugin Marketplace</div>
                </a>
            </div>

            {{-- Plugin icon and title --}}
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
                @if ($plugin->hasLogo())
                    <img
                        src="{{ $plugin->getLogoUrl() }}"
                        alt="{{ $plugin->name }} logo"
                        class="size-16 shrink-0 rounded-2xl object-cover"
                    />
                @elseif ($plugin->hasGradientIcon())
                    <div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white">
                        <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-8" />
                    </div>
                @else
                    <div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                        <x-vaadin-plug class="size-8" />
                    </div>
                @endif
                <div>
                    <h1
                        id="plugin-title"
                        class="font-mono text-2xl font-bold sm:text-3xl"
                    >
                        {{ $plugin->name }}
                    </h1>
                    @if ($plugin->description)
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            {{ $plugin->description }}
                        </p>
                    @endif
                </div>
            </div>
        </header>

        {{-- Divider --}}
        <x-divider />

        <div class="mt-2 flex flex-col-reverse gap-8 lg:flex-row lg:items-start">
            {{-- Main content - README --}}
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
                aria-labelledby="plugin-title"
            >
                @if ($plugin->readme_html)
                    {!! $plugin->readme_html !!}
                @else
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-slate-800/50">
                        <p class="text-gray-500 dark:text-gray-400">
                            README not available yet.
                        </p>
                    </div>
                @endif
            </article>

            {{-- Sidebar - Plugin details --}}
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
                {{-- Purchase Box for Paid Plugins --}}
                @if ($plugin->isPaid() && $bestPrice && $plugin->is_active)
                    <div class="mb-4 rounded-2xl border-2 border-indigo-500 bg-gradient-to-br from-indigo-50 to-purple-50 p-6 dark:border-indigo-400 dark:from-indigo-950/50 dark:to-purple-950/50">
                        <div class="text-center">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Price</p>
                            @if ($hasDiscount && $regularPrice)
                                <p class="mt-1 text-lg text-gray-400 line-through dark:text-gray-500">
                                    ${{ number_format($regularPrice->amount / 100) }}
                                </p>
                                <p class="text-4xl font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($bestPrice->amount / 100) }}
                                </p>
                                <p class="mt-1 text-xs font-medium text-green-600 dark:text-green-400">
                                    {{ $bestPrice->tier->label() }} pricing applied
                                </p>
                            @else
                                <p class="mt-1 text-4xl font-bold text-gray-900 dark:text-white">
                                    ${{ number_format($bestPrice->amount / 100) }}
                                </p>
                            @endif
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">One-time purchase</p>
                        </div>
                        <form action="{{ route('cart.add', $plugin->routeParams()) }}" method="POST" class="mt-4">
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 hover:shadow-indigo-500/40 dark:shadow-indigo-500/10"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                </svg>
                                Add to Cart
                            </button>
                        </form>
                    </div>
                @endif

                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-slate-800/50">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Plugin Details
                    </h2>

                    <dl class="mt-4 grid grid-cols-2 gap-3">
                        {{-- Type --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Type</dt>
                            <dd class="mt-1">
                                @if ($plugin->isPaid())
                                    <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Paid</span>
                                @else
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Free</span>
                                @endif
                            </dd>
                        </div>

                        {{-- Version --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Version</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $plugin->latest_version ?? '—' }}
                            </dd>
                        </div>

                        {{-- iOS Version --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">iOS</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $plugin->ios_version ?? '—' }}
                            </dd>
                        </div>

                        {{-- Android Version --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Android</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $plugin->android_version ?? '—' }}
                            </dd>
                        </div>

                        {{-- Author --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Author</dt>
                            <dd class="mt-1">
                                <a
                                    href="{{ route('plugins.marketplace', ['author' => $plugin->user->id]) }}"
                                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ $plugin->user->display_name }}
                                </a>
                            </dd>
                        </div>

                        {{-- License --}}
                        <div class="rounded-xl bg-gray-50 p-3 dark:bg-slate-700/30">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">License</dt>
                            <dd class="mt-1">
                                @if ($plugin->getLicense())
                                    <a
                                        href="{{ $plugin->getLicenseUrl() }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                                    >
                                        {{ $plugin->getLicense() }}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">—</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    {{-- Links (only for free plugins with repository) --}}
                    @if ($plugin->isFree() && $plugin->repository_url)
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <a
                                href="{{ $plugin->repository_url }}"
                                target="_blank"
                                class="flex items-center justify-center gap-2 rounded-xl bg-gray-50 p-3 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:bg-slate-700/30 dark:text-gray-400 dark:hover:bg-slate-700/50"
                            >
                                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                                GitHub
                            </a>

                            <a
                                href="{{ $plugin->getPackagistUrl() }}"
                                target="_blank"
                                class="flex items-center justify-center gap-2 rounded-xl bg-gray-50 p-3 text-sm font-medium text-gray-600 transition hover:bg-gray-100 dark:bg-slate-700/30 dark:text-gray-400 dark:hover:bg-slate-700/50"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                Packagist
                            </a>
                        </div>
                    @endif

                    @if ($plugin->last_synced_at)
                        <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                Last updated {{ $plugin->last_synced_at->diffForHumans() }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Bundles containing this plugin --}}
                @if ($bundles->isNotEmpty())
                    <div class="mt-4 rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-6 dark:border-amber-700 dark:from-amber-950/30 dark:to-orange-950/30">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-amber-700 dark:text-amber-400">
                            Available in Bundles
                        </h2>
                        <ul class="mt-4 space-y-3">
                            @foreach ($bundles as $bundle)
                                <li>
                                    <a
                                        href="{{ route('bundles.show', $bundle) }}"
                                        class="group block rounded-xl bg-white/60 p-3 transition hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800"
                                    >
                                        <div class="flex items-center gap-3">
                                            @if ($bundle->hasLogo())
                                                <img
                                                    src="{{ $bundle->getLogoUrl() }}"
                                                    alt="{{ $bundle->name }}"
                                                    class="size-10 shrink-0 rounded-lg object-cover"
                                                />
                                            @else
                                                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-gray-900 group-hover:text-amber-700 dark:text-white dark:group-hover:text-amber-400">
                                                    {{ $bundle->name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $bundle->formatted_price }}
                                                    @if ($bundle->discount_percent > 0)
                                                        <span class="text-green-600 dark:text-green-400">· Save {{ $bundle->discount_percent }}%</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</x-layout>
