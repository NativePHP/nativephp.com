<x-layout title="{{ $product->name }}">
    <section
        class="mx-auto mt-10 w-full max-w-7xl"
        aria-labelledby="product-title"
    >
        <header class="relative">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full bg-purple-300/50 blur-[150px] md:w-80 dark:bg-purple-500/30"
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

            {{-- Product icon and title --}}
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
                @if ($product->logo_path)
                    <img
                        src="{{ Storage::url($product->logo_path) }}"
                        alt="{{ $product->name }}"
                        class="size-16 shrink-0 rounded-2xl object-cover"
                    >
                @else
                    <div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white">
                        <x-heroicon-s-cube class="size-8" />
                    </div>
                @endif
                <div>
                    <h1
                        id="product-title"
                        class="font-mono text-2xl font-bold sm:text-3xl"
                    >
                        {{ $product->name }}
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Build native NativePHP plugins with AI assistance
                    </p>
                </div>
            </div>
        </header>

        {{-- Divider --}}
        <x-divider />

        {{-- Session Messages --}}
        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        <div class="mt-2 flex flex-col-reverse gap-8 lg:flex-row lg:items-start">
            {{-- Main content --}}
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
                class="prose min-w-0 max-w-none grow dark:prose-invert prose-headings:font-semibold prose-h2:text-xl prose-h2:mt-8 prose-h2:mb-4 prose-p:text-gray-600 dark:prose-p:text-gray-400 prose-li:text-gray-600 dark:prose-li:text-gray-400"
            >
                {{-- Hero Section --}}
                <div class="not-prose mb-10 rounded-2xl bg-gradient-to-br from-purple-50 to-indigo-50 p-8 dark:from-purple-950/30 dark:to-indigo-950/30">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Skip the Kotlin & Swift learning curve
                    </h2>
                    <p class="mt-3 text-lg text-gray-600 dark:text-gray-400">
                        The Plugin Dev Kit is a <a href="https://claude.com/claude-code" target="_blank" class="text-purple-600 hover:underline dark:text-purple-400">Claude Code</a> plugin that scaffolds complete NativePHP plugin packages for you. Describe what you want in plain English and get production-ready native code for both platforms.
                    </p>
                </div>

                {{-- Installation --}}
                <h2>Install in 2 Steps</h2>
                <div class="not-prose mt-4 space-y-3">
                    <div class="overflow-x-auto rounded-xl bg-gray-900 p-4 dark:bg-black">
                        <pre class="text-sm text-gray-100"><code><span class="text-gray-500"># Add the NativePHP plugin registry</span>
<span class="text-purple-400">claude</span> plugin marketplace add nativephp/claude-code

<span class="text-gray-500"># Install the Plugin Dev Kit</span>
<span class="text-purple-400">claude</span> plugin install nativephp-plugin-dev</code></pre>
                    </div>
                </div>
                <p>
                    That's it. The plugin is now available in every Claude Code session.
                </p>

                {{-- How to Use --}}
                <h2>Using the Plugin</h2>
                <p>
                    Once installed, just tell Claude what kind of plugin you want to build:
                </p>
                <div class="not-prose mt-4 space-y-3">
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Example prompts:</p>
                        <ul class="mt-3 space-y-2.5">
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="mt-0.5 shrink-0 text-purple-500">&raquo;</span>
                                <span>"Use the nativephp-plugin-dev plugin to create a barcode scanner plugin using ML Kit"</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="mt-0.5 shrink-0 text-purple-500">&raquo;</span>
                                <span>"Create a NativePHP plugin that wraps the HealthKit API for step counting"</span>
                            </li>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="mt-0.5 shrink-0 text-purple-500">&raquo;</span>
                                <span>"Build me a Bluetooth Low Energy plugin for NativePHP Mobile"</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- What It Creates --}}
                <h2>What It Creates</h2>
                <p>
                    Every scaffolded plugin is a complete, ready-to-develop Composer package with everything wired up:
                </p>
                <div class="not-prose mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">PHP Class & Laravel Facade</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Service provider, facade, and public API</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Kotlin (Android)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Bridge functions, Activities & Services</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Swift (iOS)</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Bridge functions & ViewControllers</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">nativephp.json Manifest</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Dependencies, features & metadata</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">JavaScript Bridge</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Client-side module for WebView calls</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Permissions & Entitlements</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Info.plist, AndroidManifest intents & activities</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-teal-100 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Lifecycle Hooks</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Build pipeline hooks for custom logic</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-slate-800/50">
                        <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Readme, Docs & AI Guidelines</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Documentation and Boost AI context</p>
                        </div>
                    </div>
                </div>
                <p>
                    The agents also understand how to <strong>broadcast events from native code back to your Laravel app and WebView</strong>, so your app can react to native callbacks in real time.
                </p>

                {{-- Tips --}}
                <h2>Tips for Best Results</h2>

                <h3>Tell it which SDK you're wrapping</h3>
                <p>
                    If you're wrapping a specific native SDK, link to its documentation so the agent can reference the API surface. For example: <em>"Build a plugin wrapping <a href="https://developers.google.com/ml-kit" target="_blank">Google ML Kit</a> for text recognition."</em>
                </p>

                <h3>Specify the package manager dependency</h3>
                <p>
                    Tell the agent exactly which native packages to use so it can configure the manifest correctly:
                </p>
                <ul>
                    <li><strong>Gradle:</strong> <code>com.google.mlkit:text-recognition:16.0.0</code></li>
                    <li><strong>CocoaPods:</strong> <code>GoogleMLKit/TextRecognition</code></li>
                    <li><strong>Swift Package Manager:</strong> provide the repository URL</li>
                </ul>

                <h3>Be specific about what you need</h3>
                <p>
                    The more detail you give, the better the result. Mention specific features, permissions, or platform behaviors you care about.
                </p>

                {{-- Private Repository --}}
                @if ($product->github_repo)
                    <h2>Private Repository Access</h2>
                    <p>
                        Your purchase includes access to the private <code>nativephp/{{ $product->github_repo }}</code> repository containing:
                    </p>
                    <ul>
                        <li><strong>Complete plugin examples</strong> — Real-world plugins you can learn from and customize</li>
                        <li><strong>Agent definition files</strong> — Install directly into your Claude Code environment</li>
                        <li><strong>Reference implementations</strong> — Camera, ML Kit, Bluetooth, and more</li>
                        <li><strong>Ongoing updates</strong> — New agents and examples as NativePHP evolves</li>
                    </ul>
                @endif

                {{-- Who It's For --}}
                <h2>Perfect For</h2>
                <ul>
                    <li><strong>Laravel developers</strong> who want to extend NativePHP without learning native development</li>
                    <li><strong>Teams</strong> building custom functionality for their mobile apps</li>
                    <li><strong>Agencies</strong> delivering NativePHP projects with native integrations</li>
                    <li><strong>Plugin authors</strong> who want to ship faster and with fewer bugs</li>
                </ul>
            </article>

            {{-- Sidebar --}}
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
                class="w-full shrink-0 lg:sticky lg:top-24 lg:w-80"
            >
                @if ($alreadyOwned)
                    {{-- Already Owned --}}
                    <div class="rounded-2xl border-2 border-green-500 bg-gradient-to-br from-green-50 to-emerald-50 p-6 dark:border-green-400 dark:from-green-950/50 dark:to-emerald-950/50">
                        <div class="text-center">
                            <div class="mx-auto grid size-12 place-items-center rounded-full bg-green-100 dark:bg-green-900/50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6 text-green-600 dark:text-green-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="mt-3 font-semibold text-green-800 dark:text-green-200">You Own This Product</p>
                            <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                Access your repository from the Integrations page.
                            </p>
                        </div>
                        @if ($product->github_repo)
                            <a
                                href="{{ route('customer.integrations') }}"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-green-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-green-700"
                            >
                                <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                </svg>
                                Manage Repository Access
                            </a>
                        @endif
                    </div>
                @else
                    {{-- Purchase Box --}}
                    @if ($bestPrice)
                        <div class="rounded-2xl border-2 border-purple-500 bg-gradient-to-br from-purple-50 to-indigo-50 p-6 dark:border-purple-400 dark:from-purple-950/50 dark:to-indigo-950/50">
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
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">One-time purchase · Lifetime access</p>
                            </div>
                            <form action="{{ route('cart.product.add', $product) }}" method="POST" class="mt-4">
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-purple-600 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-purple-500/25 transition hover:bg-purple-700 hover:shadow-purple-500/40 dark:shadow-purple-500/10"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                    Add to Cart
                                </button>
                            </form>
                            <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                Secure checkout via Stripe
                            </p>
                        </div>
                    @endif
                @endif

                {{-- Product Details --}}
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-slate-800/50">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        What You Get
                    </h2>

                    <ul class="mt-4 space-y-3">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Kotlin/Android expert agent</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Swift/iOS expert agent</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Plugin architect agent</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Validation & testing tools</span>
                        </li>
                        @if ($product->github_repo)
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Private GitHub repository access</span>
                            </li>
                        @endif
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-purple-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Lifetime updates</span>
                        </li>
                    </ul>
                </div>

                {{-- Requirements --}}
                <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-slate-800/50">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Requirements
                    </h2>

                    <ul class="mt-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <a href="https://claude.com/claude-code" target="_blank" class="text-purple-600 hover:underline dark:text-purple-400">Claude Code</a>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            NativePHP Mobile
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="size-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                            GitHub account
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-layout>
