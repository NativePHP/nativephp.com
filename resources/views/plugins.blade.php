<x-layout title="Plugins for NativePHP Mobile">
    <div class="mx-auto max-w-5xl">
        {{-- Hero Section --}}
        <section class="mt-12">
            <div class="grid place-items-center text-center">
                {{-- Icon --}}
                <div
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
                >
                    <div class="mx-auto grid size-20 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-lg">
                        <x-icons.puzzle class="size-10" />
                    </div>
                </div>

                {{-- Title --}}
                <h1
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mt-6 text-4xl md:text-5xl lg:text-6xl"
                >
                    Mobile
                    <span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">
                        {
                    </span>
                    <span class="font-bold">Plugins</span>
                    <span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">
                        }
                    </span> Rock
                </h1>

                {{-- Subtitle --}}
                <p
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mx-auto mt-6 max-w-3xl text-lg text-gray-600 dark:text-zinc-400"
                >
                    Extend your NativePHP Mobile apps with powerful native features.
                    Install with Composer. Build <em>anything</em> for iOS and Android.
                </p>

                {{-- Call to Action Buttons --}}
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    Array.from($el.children),
                                    {
                                        y: [10, 0],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.backOut,
                                        delay: motion.stagger(0.2),
                                    },
                                )
                            })
                        }
                    "
                    class="mt-8 flex w-full flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    {{-- Primary CTA - Browse Plugins --}}
                    <div class="w-full max-w-56">
                        <a
                            href="{{ route('plugins.directory') }}"
                            class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                        >
                            <x-icons.puzzle class="size-5" />
                            Browse Plugins
                        </a>
                    </div>

                    {{-- Secondary CTA - Documentation --}}
                    <div class="w-full max-w-56">
                        <a
                            href="/docs/mobile/2/plugins"
                            class="flex items-center justify-center gap-2.5 rounded-xl bg-gray-200 px-6 py-4 text-gray-800 transition duration-200 hover:bg-gray-300/80 dark:bg-slate-700/30 dark:text-white dark:hover:bg-slate-700/40"
                        >
                            <x-icons.docs class="size-5" />
                            Read the Docs
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Featured Plugins Section --}}
        @if ($featuredPlugins->isNotEmpty())
        <section class="mt-24">
            <div
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
            >
                <h2 class="text-center text-2xl font-semibold md:text-3xl">
                    Featured Plugins
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                    Hand-picked plugins to supercharge your mobile apps.
                </p>

                {{-- Plugin Cards Grid --}}
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($featuredPlugins as $plugin)
                        <x-plugin-card :plugin="$plugin" />
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.puzzle class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                Featured plugins coming soon
                            </p>
                        </div>
                        <div class="hidden flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center md:flex dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.puzzle class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                Featured plugins coming soon
                            </p>
                        </div>
                        <div class="hidden flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center lg:flex dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.puzzle class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                Featured plugins coming soon
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
        @endif

        {{-- Plugin Bundles Section --}}
        @if ($bundles->isNotEmpty())
        <section class="mt-16">
            <div
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
            >
                <h2 class="text-center text-2xl font-semibold md:text-3xl">
                    Plugin Bundles
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                    Save money with curated plugin collections.
                </p>

                {{-- Bundle Cards Grid --}}
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($bundles as $bundle)
                        <x-bundle-card :bundle="$bundle" />
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        {{-- Latest Plugins Section --}}
        <section class="mt-16">
            <div
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
            >
                <h2 class="text-center text-2xl font-semibold md:text-3xl">
                    Latest Plugins
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                    Freshly released plugins from our community.
                </p>

                {{-- Plugin Cards Grid --}}
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @forelse ($latestPlugins as $plugin)
                        <x-plugin-card :plugin="$plugin" />
                    @empty
                        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.sparkles class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                New plugins coming soon
                            </p>
                        </div>
                        <div class="hidden flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center md:flex dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.sparkles class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                New plugins coming soon
                            </p>
                        </div>
                        <div class="hidden flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-8 text-center lg:flex dark:border-gray-700 dark:bg-slate-800/50">
                            <x-icons.sparkles class="size-8 text-gray-400 dark:text-gray-500" />
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                New plugins coming soon
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- Benefits Section --}}
        <section class="mt-24">
            <div
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
                class="text-center"
            >
                <h2 class="text-2xl font-semibold md:text-3xl">
                    Why Use Plugins?
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-zinc-400">
                    Unlock native capabilities without leaving Laravel.
                </p>
            </div>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [10, 0],
                                    opacity: [0, 1],
                                    scale: [0.8, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.1),
                                },
                            )
                        })
                    }
                "
                class="mt-12 grid gap-x-8 gap-y-12 md:grid-cols-2 lg:grid-cols-3"
            >
                {{-- Card - Composer Install --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">One Command Install</x-slot>

                    <x-slot name="description">
                        Add native features with a single <code class="rounded bg-gray-200 px-1.5 py-0.5 text-sm dark:bg-slate-700">composer require</code>. No Xcode or Android Studio knowledge required.
                    </x-slot>
                </x-benefit-card>

                {{-- Card - Build Anything --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">Build Anything</x-slot>

                    <x-slot name="description">
                        There's no limit to what plugins can do. Access any native API, sensor, or hardware feature on iOS and Android.
                    </x-slot>
                </x-benefit-card>

                {{-- Card - Auto-Registered --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">Auto-Registered</x-slot>

                    <x-slot name="description">
                        Plugins are automatically discovered and registered. Just enable them in your config and you're ready to go.
                    </x-slot>
                </x-benefit-card>

                {{-- Card - Platform Dependencies --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">Native Dependencies</x-slot>

                    <x-slot name="description">
                        Plugins can add Gradle dependencies, CocoaPods, and Swift Package Manager packages automatically.
                    </x-slot>
                </x-benefit-card>

                {{-- Card - Lifecycle Hooks --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">Build Lifecycle Hooks</x-slot>

                    <x-slot name="description">
                        Hook into critical moments in the build pipeline. Run custom logic before, during, or after builds.
                    </x-slot>
                </x-benefit-card>

                {{-- Card - Security --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="size-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"
                            />
                        </svg>
                    </x-slot>

                    <x-slot name="title">Security First</x-slot>

                    <x-slot name="description">
                        Security is our top priority. Plugins are sandboxed and permissions are explicit, keeping your users safe.
                    </x-slot>
                </x-benefit-card>
            </div>
        </section>

        {{-- For Plugin Authors Section --}}
        <section class="mt-24">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gray-100 p-12 dark:bg-[#1a1a2e]"
            >
                <h2 class="text-3xl font-semibold">
                    Build & Sell Your Own Plugins
                </h2>

                <p class="mt-4 max-w-3xl text-gray-600 dark:text-gray-400">
                    Know Swift or Kotlin? Create plugins for the NativePHP community and generate revenue from your expertise.
                </p>

                <div class="mt-8 space-y-5">
                    <div class="flex gap-3.5">
                        <div
                            class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-medium">
                                Write Swift & Kotlin
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                Build the native code and PHP bridging layer. We handle the rest, mapping everything so it just works.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3.5">
                        <div
                            class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-medium">
                                Full Laravel Power
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                Set permissions, create config files, publish views, and do everything a Laravel package can do.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3.5">
                        <div
                            class="mt-0.5 grid size-8 shrink-0 place-items-center self-start rounded-xl bg-[#cbe7d8] dark:bg-indigo-400 dark:text-black"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-medium">
                                Sell Your Plugins (Soon!)
                            </h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-400">
                                Sell your plugins through our marketplace and earn money from your native development skills.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <a
                        href="/docs/mobile/2/plugins"
                        class="inline-flex items-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                    >
                        <x-icons.docs class="size-5" />
                        Learn to Build Plugins
                    </a>
                </div>
            </div>
        </section>

        {{-- Call to Action Section --}}
        <section class="mt-16 pb-24">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="dark:bg-mirage rounded-2xl bg-gray-100 p-10 text-center"
            >
                <h2 class="text-3xl font-semibold">
                    Ready to Extend Your App?
                </h2>

                <p
                    class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-gray-400"
                >
                    Discover plugins that add powerful native features to your NativePHP Mobile apps, or start building your own today.
                </p>

                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    Array.from($el.children),
                                    {
                                        y: [10, 0],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.backOut,
                                        delay: motion.stagger(0.2),
                                    },
                                )
                            })
                        }
                    "
                    class="mt-6 flex w-full flex-col items-center justify-center gap-4 sm:flex-row"
                >
                    {{-- Primary CTA --}}
                    <div class="w-full max-w-56">
                        <a
                            href="{{ route('plugins.directory') }}"
                            class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                        >
                            <x-icons.puzzle class="size-5" />
                            Browse Plugins
                        </a>
                    </div>

                    {{-- Secondary CTA --}}
                    <div class="w-full max-w-56">
                        <a
                            href="/docs/mobile/2/plugins"
                            class="flex items-center justify-center gap-2.5 rounded-xl bg-gray-200 px-6 py-4 text-gray-800 transition duration-200 hover:bg-gray-300/80 dark:bg-slate-700/30 dark:text-white dark:hover:bg-slate-700/40"
                        >
                            <x-icons.docs class="size-5" />
                            Read the Docs
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
