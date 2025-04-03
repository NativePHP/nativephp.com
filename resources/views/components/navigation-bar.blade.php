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
                    x-init="
                        () => {
                            motion.hover($el, (element) => {
                                motion.animate(
                                    $refs.sponsorHeart1,
                                    {
                                        y: -8,
                                        x: 6,
                                        opacity: 1,
                                        scale: 1,
                                    },
                                    {
                                        duration: 0.25,
                                        ease: motion.backOut,
                                    },
                                )
                                motion.animate(
                                    $refs.sponsorHeart2,
                                    {
                                        y: -15,
                                        x: -9,
                                        opacity: 1,
                                        scale: 1,
                                        rotate: -20,
                                    },
                                    {
                                        duration: 0.25,
                                        ease: motion.backOut,
                                        delay: 0.05,
                                    },
                                )
                                motion.animate(
                                    $refs.sponsorHeart3,
                                    {
                                        y: -16,
                                        x: 7,
                                        opacity: 1,
                                        scale: 1,
                                        rotate: 20,
                                    },
                                    {
                                        duration: 0.25,
                                        ease: motion.backOut,
                                        delay: 0.1,
                                    },
                                )

                                return () =>
                                    motion.animate(
                                        [$refs.sponsorHeart1, $refs.sponsorHeart2, $refs.sponsorHeart3],
                                        {
                                            y: 0,
                                            x: 0,
                                            opacity: 0,
                                            scale: 0,
                                            rotate: 0,
                                        },
                                        {
                                            duration: 0.25,
                                            ease: motion.backIn,
                                        },
                                    )
                            })
                        }
                    "
                    href="/docs/1/getting-started/sponsoring"
                    class="relative hidden bg-gradient-to-tr from-violet-600 to-violet-300 bg-clip-text font-medium text-transparent lg:block dark:from-violet-500 dark:to-white/80"
                    aria-label="Sponsor NativePHP"
                    title="Support NativePHP development"
                >
                    Sponsor
                    <span class="sr-only">NativePHP on GitHub</span>

                    {{-- Heart 1 --}}
                    <div
                        x-ref="sponsorHeart1"
                        class="absolute right-1/2 top-0 origin-center scale-0 opacity-0"
                        aria-hidden="true"
                    >
                        <x-icons.heart class="size-[9px] text-violet-400" />
                    </div>

                    {{-- Heart 2 --}}
                    <div
                        x-ref="sponsorHeart2"
                        class="absolute left-1/2 top-0 origin-center scale-0 opacity-0"
                        aria-hidden="true"
                    >
                        <x-icons.heart class="size-[7px] text-violet-400" />
                    </div>

                    {{-- Heart 3 --}}
                    <div
                        x-ref="sponsorHeart3"
                        class="absolute right-1/2 top-0 origin-center scale-0 opacity-0"
                        aria-hidden="true"
                    >
                        <x-icons.heart class="size-[5px] text-violet-400" />
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
                        class="absolute -bottom-1 left-0 h-0.5 w-2 origin-left rounded-full bg-violet-400 will-change-transform dark:bg-violet-500"
                        aria-hidden="true"
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
                        class="absolute -bottom-1.5 left-0 h-8 w-2 origin-left rounded-full bg-gradient-to-t from-violet-500 to-transparent blur-[9px] will-change-transform dark:blur-sm"
                        aria-hidden="true"
                    ></div>
                </a>

                {{-- Theme toggle --}}
                <x-theme-toggle />
            </div>
        </div>
    </div>
</nav>
