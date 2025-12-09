<x-layout title="NativePHP vs Flutter">
    <div class="mx-auto max-w-5xl">
        {{-- Hero Section --}}
        <section
            class="mt-12"
            aria-labelledby="hero-heading"
        >
            <div class="grid place-items-center text-center">
                {{-- Title --}}
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
                    class="text-4xl font-bold md:text-5xl lg:text-6xl"
                >
                    <span class="text-gray-400">NativePHP</span>
                    <span class="text-gray-300 dark:text-gray-600">vs</span>
                    <span
                        class="bg-gradient-to-r from-sky-400 to-blue-500 bg-clip-text text-transparent"
                    >
                        Flutter
                    </span>
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
                    class="mx-auto mt-6 max-w-3xl text-lg text-gray-600 dark:text-gray-400"
                >
                    Build native mobile apps without learning a new language.
                    Use the PHP and Laravel skills you already have.
                </p>
            </div>
        </section>

        {{-- Quick Stats Section --}}
        <section
            class="mt-16"
            aria-labelledby="stats-heading"
        >
            <h2
                id="stats-heading"
                class="sr-only"
            >
                Quick comparison stats
            </h2>
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [20, 0],
                                    opacity: [0, 1],
                                    scale: [0.95, 1],
                                },
                                {
                                    duration: 0.6,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.1),
                                },
                            )
                        })
                    }
                "
                class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
            >
                {{-- Stat Card --}}
                <div
                    class="rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 text-center dark:from-emerald-950/50 dark:to-emerald-900/30"
                >
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                        ~50MB
                    </div>
                    <div class="mt-2 text-sm text-emerald-700 dark:text-emerald-300">
                        NativePHP Download
                    </div>
                </div>

                {{-- Stat Card --}}
                <div
                    class="rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center dark:from-gray-900 dark:to-gray-800"
                >
                    <div class="text-4xl font-bold text-gray-600 dark:text-gray-400">
                        3GB+
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Flutter SDK + Tools
                    </div>
                </div>

                {{-- Stat Card --}}
                <div
                    class="rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 text-center dark:from-emerald-950/50 dark:to-emerald-900/30"
                >
                    <div class="text-4xl font-bold text-emerald-600 dark:text-emerald-400">
                        0
                    </div>
                    <div class="mt-2 text-sm text-emerald-700 dark:text-emerald-300">
                        New Languages to Learn
                    </div>
                </div>

                {{-- Stat Card --}}
                <div
                    class="rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 p-6 text-center dark:from-gray-900 dark:to-gray-800"
                >
                    <div class="text-4xl font-bold text-gray-600 dark:text-gray-400">
                        1
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Dart Required for Flutter
                    </div>
                </div>
            </div>
        </section>

        {{-- Developer Experience Section --}}
        <section
            class="mt-20"
            aria-labelledby="dx-heading"
        >
            <header class="text-center">
                <h2
                    id="dx-heading"
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
                    class="text-3xl font-semibold"
                >
                    Developer Experience Comparison
                </h2>
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
                    class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400"
                >
                    See how NativePHP simplifies mobile development compared to
                    Flutter.
                </p>
            </header>

            {{-- Comparison Cards --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mt-10 grid gap-6 lg:grid-cols-2"
            >
                {{-- NativePHP Card --}}
                <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white p-8 ring-1 ring-emerald-200 dark:from-emerald-950/30 dark:to-gray-900 dark:ring-emerald-800/50">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 place-items-center">
                            <x-mini-logo class="size-10" />
                        </div>
                        <h3 class="text-xl font-semibold text-emerald-700 dark:text-emerald-400">
                            NativePHP
                        </h3>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                                <x-icons.checkmark class="size-4" />
                            </div>
                            <div>
                                <div class="font-medium">
                                    Use your existing PHP/Laravel skills
                                </div>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    No need to learn Dart or Flutter's widget system
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                                <x-icons.checkmark class="size-4" />
                            </div>
                            <div>
                                <div class="font-medium">
                                    ~50MB total download
                                </div>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Just add Xcode and Android Studio
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                                <x-icons.checkmark class="size-4" />
                            </div>
                            <div>
                                <div class="font-medium">
                                    Leverage Laravel ecosystem
                                </div>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Eloquent, Blade, Livewire, and thousands of packages
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                                <x-icons.checkmark class="size-4" />
                            </div>
                            <div>
                                <div class="font-medium">
                                    Easily share code with your web app
                                </div>
                                <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    Reuse your PHP models, services, and business logic
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Flutter Card --}}
                <div class="rounded-2xl bg-gradient-to-br from-gray-50 to-white p-8 ring-1 ring-gray-200 dark:from-gray-900 dark:to-gray-950 dark:ring-gray-800">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 place-items-center rounded-xl bg-sky-500 text-white">
                            <svg
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="size-6"
                            >
                                <path
                                    d="M14.314 0L2.3 12 6 15.7 21.684.013h-7.357L14.314 0zm.014 11.072L7.857 17.53l6.47 6.47H21.7l-6.46-6.468 6.46-6.46h-7.37z"
                                />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300">
                            Flutter
                        </h3>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-700 dark:text-gray-300">
                                    Must learn Dart programming
                                </div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                                    A completely new language and paradigm
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-700 dark:text-gray-300">
                                    3GB+ SDK download
                                </div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                                    Plus Flutter SDK, Dart, and Android SDK
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-700 dark:text-gray-300">
                                    Separate ecosystem
                                </div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                                    pub.dev packages, different from your backend
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="size-4"
                                >
                                    <path
                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-700 dark:text-gray-300">
                                    Slow first builds
                                </div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-500">
                                    First flutter run downloads and compiles extensively
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Native Features Grid --}}
        <x-comparison.native-features />

        {{-- Size Comparison Charts --}}
        <section
            class="mt-20"
            aria-labelledby="size-heading"
        >
            <header class="text-center">
                <h2
                    id="size-heading"
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
                    class="text-3xl font-semibold"
                >
                    Size & Speed Comparison
                </h2>
            </header>

            <div class="mt-10 grid gap-8 lg:grid-cols-2 xl:grid-cols-3">
                {{-- SDK Download Size Chart --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-mirage">
                    <h3 class="text-lg font-semibold">
                        SDK Download Size
                    </h3>
                    <div class="mt-6">
                        <x-comparison.bar-chart
                            :items="[
                                ['name' => 'NativePHP', 'value' => '~50', 'percentage' => 2, 'highlight' => true],
                                ['name' => 'Flutter SDK', 'value' => '1,640+', 'percentage' => 55, 'highlight' => false],
                                ['name' => 'Flutter + Tools', 'value' => '3,000+', 'percentage' => 100, 'highlight' => false],
                            ]"
                            unit="MB"
                        />
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Flutter requires the Dart SDK, Flutter framework, and
                        often Android SDK components
                    </p>
                </div>

                {{-- App Bundle Size Chart --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-mirage">
                    <h3 class="text-lg font-semibold">
                        Minimum App Size
                    </h3>
                    <div class="mt-6">
                        <x-comparison.bar-chart
                            :items="[
                                ['name' => 'NativePHP', 'value' => '~30', 'percentage' => 100, 'highlight' => true],
                                ['name' => 'Flutter', 'value' => '4+', 'percentage' => 13, 'highlight' => false],
                            ]"
                            unit="MB"
                        />
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        App size varies widely based on bundled features, assets
                        and platform optimizations
                    </p>
                </div>

                {{-- First Boot Time Chart --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-mirage">
                    <h3 class="text-lg font-semibold">
                        First Boot Time
                    </h3>
                    <div class="mt-6">
                        <x-comparison.bar-chart
                            :items="[
                                ['name' => 'NativePHP', 'value' => '~5', 'percentage' => 33, 'highlight' => true],
                                ['name' => 'Flutter', 'value' => '10-15', 'percentage' => 100, 'highlight' => false],
                            ]"
                            unit="s"
                        />
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        Cold start after fresh install
                    </p>
                </div>
            </div>
        </section>

        {{-- Getting Started Comparison --}}
        <section
            class="mt-20"
            aria-labelledby="setup-heading"
        >
            <header class="text-center">
                <h2
                    id="setup-heading"
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
                    class="text-3xl font-semibold"
                >
                    Getting Started
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    Compare the setup process side by side
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mt-10 grid gap-6 lg:grid-cols-2"
            >
                {{-- NativePHP Setup --}}
                <div class="rounded-2xl bg-emerald-50 p-8 ring-1 ring-emerald-200 dark:bg-emerald-950/20 dark:ring-emerald-800/50">
                    <h3 class="flex items-center gap-2 text-lg font-semibold text-emerald-700 dark:text-emerald-400">
                        <x-mini-logo class="size-5" />
                        NativePHP Setup
                    </h3>
                    <div class="mt-6 space-y-3 font-mono text-sm">
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-emerald-600 dark:text-emerald-400">$</span>
                            composer require nativephp/mobile
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-emerald-600 dark:text-emerald-400">$</span>
                            php artisan native:install
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-emerald-600 dark:text-emerald-400">$</span>
                            php artisan native:run
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-emerald-700 dark:text-emerald-300">
                        That's it. Your app is running.
                    </p>
                </div>

                {{-- Flutter Setup --}}
                <div class="rounded-2xl bg-gray-50 p-8 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
                        Flutter Setup
                    </h3>
                    <div class="mt-6 space-y-3 font-mono text-sm">
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-gray-400">#</span>
                            Download & extract Flutter SDK (~1.6GB)
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-gray-400">$</span>
                            export PATH="$PATH:/path/to/flutter/bin"
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-gray-400">$</span>
                            flutter doctor
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-gray-400">$</span>
                            flutter create my_app
                        </div>
                        <div class="rounded-lg bg-white/80 px-4 py-3 dark:bg-black/30">
                            <span class="text-gray-400">$</span>
                            cd my_app && flutter run
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                        First run downloads Dart SDK and compiles the engine...
                    </p>
                </div>
            </div>
        </section>

        {{-- Language Comparison --}}
        <section
            class="mt-20"
            aria-labelledby="language-heading"
        >
            <header class="text-center">
                <h2
                    id="language-heading"
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
                    class="text-3xl font-semibold"
                >
                    Use What You Know
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    Why learn a new language when you can build mobile apps
                    with PHP?
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mt-10 grid gap-6 lg:grid-cols-2"
            >
                {{-- PHP Code Example --}}
                <div class="rounded-2xl bg-emerald-50 p-6 ring-1 ring-emerald-200 dark:bg-emerald-950/20 dark:ring-emerald-800/50">
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                        <x-mini-logo class="size-4" />
                        NativePHP (PHP)
                    </h3>
                    <pre class="mt-4 overflow-x-auto rounded-lg bg-white/80 p-4 text-sm dark:bg-black/30"><code class="text-gray-800 dark:text-gray-200">&lt;button
    wire:click="increment"
    class="btn btn-primary"
&gt;
    Count: @{{ $count }}
&lt;/button&gt;</code></pre>
                </div>

                {{-- Dart Code Example --}}
                <div class="rounded-2xl bg-gray-50 p-6 ring-1 ring-gray-200 dark:bg-gray-900 dark:ring-gray-800">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Flutter (Dart)
                    </h3>
                    <pre class="mt-4 overflow-x-auto rounded-lg bg-white/80 p-4 text-sm dark:bg-black/30"><code class="text-gray-800 dark:text-gray-200">ElevatedButton(
  onPressed: () {
    setState(() {
      _count++;
    });
  },
  child: Text('Count: $_count'),
)</code></pre>
                </div>
            </div>
        </section>

        {{-- Video Comparison Section --}}
        {{--
        <section
            class="mt-20"
            aria-labelledby="video-heading"
        >
            <header class="text-center">
                <h2
                    id="video-heading"
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
                    class="text-3xl font-semibold"
                >
                    See the Difference
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    Watch real apps boot up side by side
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mt-10 grid gap-6 lg:grid-cols-2"
            >
                <x-comparison.video-placeholder
                    title="NativePHP App Boot"
                    description="Video coming soon"
                />
                <x-comparison.video-placeholder
                    title="Flutter App Boot"
                    description="Video coming soon"
                />
            </div>
        </section>
        --}}

        {{-- Bifrost Section --}}
        <section
            class="mt-20"
            aria-labelledby="bifrost-heading"
        >
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    scale: [0.98, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative isolate overflow-hidden rounded-3xl bg-gradient-to-br from-violet-600 to-indigo-700 p-10 text-white lg:p-14"
            >
                {{-- Background decoration --}}
                <div
                    class="absolute -top-24 -right-24 size-64 rounded-full bg-white/10 blur-3xl"
                    aria-hidden="true"
                ></div>
                <div
                    class="absolute -bottom-24 -left-24 size-64 rounded-full bg-black/10 blur-3xl"
                    aria-hidden="true"
                ></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-8"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.75a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .913-.143Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <h2
                            id="bifrost-heading"
                            class="text-2xl font-bold lg:text-3xl"
                        >
                            Supercharge with Bifrost
                        </h2>
                    </div>

                    <p class="mt-4 max-w-2xl text-lg text-violet-100">
                        Bifrost is our first-party Continuous Deployment
                        platform that integrates tightly with NativePHP. Get
                        your apps built and into the stores in
                        <strong>minutes</strong>
                        , not hours.
                    </p>

                    <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                        <li class="flex items-center gap-2">
                            <x-icons.checkmark class="size-5 text-violet-200" />
                            <span>Cloud builds for iOS & Android</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icons.checkmark class="size-5 text-violet-200" />
                            <span>Automatic code signing</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icons.checkmark class="size-5 text-violet-200" />
                            <span>One-click App Store submission</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-icons.checkmark class="size-5 text-violet-200" />
                            <span>Team collaboration built-in</span>
                        </li>
                    </ul>

                    <div class="mt-8">
                        <a
                            href="https://bifrost.nativephp.com"
                            class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 font-semibold text-violet-700 transition hover:bg-violet-50"
                        >
                            Learn more about Bifrost
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA Section --}}
        <section
            class="mt-20 pb-24"
            aria-labelledby="cta-heading"
        >
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [20, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gray-100 p-10 text-center dark:bg-mirage"
            >
                <h2
                    id="cta-heading"
                    class="text-3xl font-semibold"
                >
                    Ready to Try NativePHP?
                </h2>
                <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-gray-400">
                    Skip learning Dart. Build native mobile apps with the PHP
                    skills you already have.
                </p>
                <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a
                        href="{{ route('pricing') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-8 py-4 font-semibold text-white transition hover:bg-emerald-700"
                    >
                        Get Started
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            class="size-5"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </a>
                    <a
                        href="{{ route('docs') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-200 px-8 py-4 font-semibold text-gray-700 transition hover:bg-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Read the Docs
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layout>
