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
                <div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                    <x-icons.puzzle class="size-8" />
                </div>
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
                class="w-full shrink-0 lg:w-72"
            >
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-slate-800/50">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Plugin Details
                    </h2>

                    <dl class="mt-4 space-y-4">
                        {{-- Type --}}
                        <div>
                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Type</dt>
                            <dd class="mt-1">
                                @if ($plugin->isPaid())
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Free
                                    </span>
                                @endif
                            </dd>
                        </div>

                        {{-- Version (placeholder) --}}
                        <div>
                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Version</dt>
                            <dd class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                —
                            </dd>
                        </div>

                        {{-- iOS Version --}}
                        @if ($plugin->ios_version)
                            <div>
                                <dt class="text-sm font-medium text-gray-900 dark:text-white">iOS</dt>
                                <dd class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $plugin->ios_version }}
                                </dd>
                            </div>
                        @endif

                        {{-- Android Version --}}
                        @if ($plugin->android_version)
                            <div>
                                <dt class="text-sm font-medium text-gray-900 dark:text-white">Android</dt>
                                <dd class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    {{ $plugin->android_version }}
                                </dd>
                            </div>
                        @endif

                        {{-- Author --}}
                        <div>
                            <dt class="text-sm font-medium text-gray-900 dark:text-white">Author</dt>
                            <dd class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $plugin->user->name }}
                            </dd>
                        </div>
                    </dl>

                    {{-- Links --}}
                    <div class="mt-6 space-y-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        @if ($plugin->repository_url)
                            <a
                                href="{{ $plugin->repository_url }}"
                                target="_blank"
                                class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                            >
                                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                                View on GitHub
                            </a>
                        @endif

                        @if ($plugin->isFree())
                            <a
                                href="{{ $plugin->getPackagistUrl() }}"
                                target="_blank"
                                class="flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                                </svg>
                                View on Packagist
                            </a>
                        @else
                            <a
                                href="{{ $plugin->getAnystackUrl() }}"
                                target="_blank"
                                class="flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                                Purchase on Anystack
                            </a>
                        @endif
                    </div>

                    @if ($plugin->last_synced_at)
                        <div class="mt-4 border-t border-gray-200 pt-4 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                Last updated {{ $plugin->last_synced_at->diffForHumans() }}
                            </p>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </section>
</x-layout>
