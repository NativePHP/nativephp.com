@props(['hasMenu' => false])
<div
    x-collapse
    x-show="!showDocsNavigation"
>
    <x-eap-banner />
</div>
<nav
    class="sticky top-0 z-50 flex flex-col items-center justify-center px-3"
    aria-label="Main Navigation"
>
    <div
        :class="{
            'ring-gray-200/80 dark:ring-gray-800/50 bg-white/50 dark:bg-white/5 translate-y-3': scrolled || showDocsNavigation,
            'ring-transparent dark:bg-transparent': ! scrolled && ! showDocsNavigation,
        }"
        class="mx-auto flex w-full max-w-5xl items-center justify-between gap-5 rounded-2xl px-5 py-4 ring-1 backdrop-blur-md transition duration-200 ease-out xl:max-w-7xl 2xl:max-w-[90rem]"
    >
        {{-- Left side --}}
        <div class="flex items-center gap-3">
            {{-- Logo --}}
            <a
                href="/"
                aria-label="NativePHP Homepage"
            >
                <x-logo class="h-4 min-[400px]:h-5 sm:h-6" />
                <span class="sr-only">NativePHP</span>
            </a>

            {{-- Version badge --}}
            <div
                class="hidden rounded-full bg-gray-200/60 px-2 py-1 text-xs text-gray-600 lg:block dark:bg-[#16182b] dark:text-[#747ee6] dark:ring-1 dark:ring-cloud"
                aria-label="Version information"
            >
                <a href="/docs/desktop/1/getting-started/releasenotes">
                    {{ $electronGitHubVersion }}
                </a>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-3.5">
            {{-- Doc search --}}
            <div>
                <div
                    id="docsearch"
                    x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
                    aria-label="Search documentation"
                ></div>
            </div>

            {{-- Desktop menu --}}
            <div
                class="flex items-center gap-3.5 text-sm"
                aria-label="Primary navigation"
            >
                {{-- Link --}}
                <a
                    href="/"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('welcome*'),
                    ])
                    aria-current="{{ request()->routeIs('welcome*') ? 'page' : 'false' }}"
                >
                    Home
                </a>

                {{-- Decorative circle --}}
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 transition duration-200 lg:block dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'hidden transition duration-200 lg:block',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('early-adopter*'),
                    ])
                    aria-current="{{ request()->routeIs('early-adopter*') ? 'page' : 'false' }}"
                >
                    Mobile
                </a>

                {{-- Decorative circle --}}
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 transition duration-200 lg:block dark:opacity-60"
                    aria-hidden="true"
                ></div>

                @if ($hasMenu)
                    <button
                        type="button"
                        @click="showDocsNavigation = !showDocsNavigation"
                        class="-m-2 block p-2 focus:outline-none focus:ring-0 lg:hidden"
                        aria-expanded="false"
                        aria-controls="docs-navigation"
                        aria-label="Toggle documentation menu"
                    >
                        <div x-show="!showDocsNavigation">
                            <x-icons.menu
                                class="h-6 w-6 text-gray-600 dark:text-gray-300"
                                aria-hidden="true"
                            />
                        </div>
                        <div x-show="showDocsNavigation">
                            <x-icons.close
                                class="h-6 w-6 text-gray-600"
                                aria-hidden="true"
                            />
                        </div>
                    </button>
                    <a
                        href="/docs/"
                        @class([
                            'hidden transition duration-200 lg:block',
                            'font-medium' => request()->is('docs*'),
                            'opacity-60 hover:opacity-100' => ! request()->is('docs*'),
                        ])
                        aria-current="{{ request()->is('docs*') ? 'page' : 'false' }}"
                    >
                        Docs
                    </a>
                @else
                    <a
                        href="/docs/"
                        @class([
                            'transition duration-200',
                            'font-medium' => request()->is('docs*'),
                            'opacity-60 hover:opacity-100' => ! request()->is('docs*'),
                        ])
                        aria-current="{{ request()->is('docs*') ? 'page' : 'false' }}"
                    >
                        Docs
                    </a>
                @endif

                {{-- Decorative circle --}}
                <div
                    class="hidden size-[3px] rotate-45 rounded-sm bg-gray-400 transition duration-200 lg:block dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="https://github.com/nativephp/laravel?sponsor=1"
                    class="group relative hidden from-[#ddb0f3] to-violet-500 bg-clip-text font-medium text-current lg:block dark:bg-gradient-to-tr dark:from-violet-500 dark:to-white/80 dark:text-transparent"
                >
                    Sponsor

                    {{-- Heart --}}
                    <div
                        class="absolute -top-2.5 right-1/2 translate-x-1/2 translate-y-5 scale-0 opacity-0 transition duration-300 ease-in-out will-change-transform group-hover:translate-y-0 group-hover:scale-100 group-hover:opacity-100"
                    >
                        <x-icons.heart class="size-2.5 text-violet-400" />
                    </div>

                    {{-- Line --}}
                    <div
                        x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        x: [-5, 50],
                                        scaleX: [1, 2.5, 1],
                                        opacity: [0, 1, 1, 1, 0],
                                    },
                                    {
                                        duration: 1.8,
                                        repeat: Infinity,
                                        repeatType: 'reverse',
                                        ease: motion.easeInOut,
                                    },
                                )
                            }
                        "
                        class="absolute -bottom-1 left-0 h-0.5 w-2 origin-left rounded-full bg-violet-500 will-change-transform"
                    ></div>

                    {{-- Blurry line --}}
                    <div
                        x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        x: [-5, 50],
                                        scaleX: [1, 2.5, 1],
                                        opacity: [0, 1, 1, 1, 0],
                                    },
                                    {
                                        duration: 1.8,
                                        repeat: Infinity,
                                        repeatType: 'reverse',
                                        ease: motion.easeInOut,
                                    },
                                )
                            }
                        "
                        class="absolute -bottom-1.5 left-0 h-8 w-2 origin-left rounded-full bg-gradient-to-t from-violet-500 to-transparent blur will-change-transform dark:blur-sm"
                    ></div>
                </a>

                {{-- Theme toggle --}}
                <x-theme-toggle />
            </div>
        </div>
    </div>
</nav>
