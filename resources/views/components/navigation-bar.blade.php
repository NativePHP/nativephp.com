<nav
    class="sticky top-0 z-50 flex flex-col items-center justify-center px-3 pt-px"
    aria-label="Main Navigation"
>
    <div
        :class="{
            'ring-gray-200/80 dark:ring-gray-700/70 bg-white/50 dark:bg-black/50 translate-y-3': scrolled || showMobileMenu,
            'ring-transparent dark:bg-transparent': ! scrolled && ! showMobileMenu,
        }"
        class="mx-auto flex w-full max-w-5xl items-center justify-between gap-5 rounded-2xl px-5 py-4 ring-1 backdrop-blur-2xl transition duration-200 ease-out xl:max-w-7xl 2xl:max-w-360"
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

            {{-- V1 Announcement --}}
            <a
                href="https://github.com/orgs/NativePHP/discussions/547"
                class="group relative z-0 hidden items-center overflow-hidden rounded-full bg-gray-200 px-2.5 py-1.5 text-xs transition duration-200 will-change-transform hover:scale-x-105 lg:inline-flex dark:bg-slate-800"
                target="_blank"
                aria-label="Read about NativePHP version 1 release"
                title="Read the NativePHP v1 announcement"
            >
                <div
                    class="@container absolute inset-0 flex items-center"
                    aria-hidden="true"
                >
                    <div
                        class="absolute h-[100cqw] w-[100cqw] animate-[spin_2.5s_linear_infinite] bg-[conic-gradient(from_0_at_50%_50%,rgba(167,139,250,0.75)_0deg,transparent_60deg,transparent_300deg,rgba(167,139,250,0.75)_360deg)] transition duration-300"
                    ></div>
                </div>

                <div
                    class="absolute inset-0.5 rounded-full bg-violet-50 dark:bg-slate-950"
                    aria-hidden="true"
                ></div>

                <div
                    class="absolute bottom-0 left-1/2 h-1/3 w-4/5 -translate-x-1/2 rounded-full bg-orange-300 opacity-50 blur-md transition-all duration-500 dark:bg-fuchsia-300/30 dark:group-hover:h-2/3 dark:group-hover:opacity-100"
                    aria-hidden="true"
                ></div>

                <span class="relative inline-flex items-center gap-1">
                    <x-icons.confetti
                        class="-mt-px size-3.5"
                        aria-hidden="true"
                    />
                    <span class="font-normal">v1 is here!</span>
                    <span class="sr-only">
                        NativePHP version 1 has been released - click to learn
                        more
                    </span>
                </span>
            </a>

            {{-- 👇 Temporarily disabled in favor of the v1 announcement button --}}
            {{-- Version badge --}}
            {{--
                <div
                class="hidden rounded-full bg-gray-200/60 px-2 py-1 text-xs text-gray-600 lg:block dark:bg-[#16182b] dark:text-[#747ee6] dark:ring-1 dark:ring-cloud"
                aria-label="Version information"
                >
                <a href="/docs/desktop/1/getting-started/releasenotes">
                {{ $electronGitHubVersion }}
                </a>
                </div>
            --}}
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-3.5">
            {{-- Doc search --}}
            <div
                class="-mr-0.5 transition-all duration-200 ease-in-out will-change-transform"
                :class="{
                    'pr-0.5': showMobileMenu,
                }"
            >
                <div
                    id="docsearch"
                    x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
                    aria-label="Search documentation"
                ></div>
            </div>

            {{-- Mobile menu --}}
            <x-mobile-menu />

            {{-- Desktop menu --}}
            <div
                class="hidden items-center gap-3.5 text-sm lg:flex"
                aria-label="Primary navigation"
            >
                {{-- Link --}}
                <a
                    href="/"
                    @class([
                        'transition duration-200',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('welcome*'),
                    ])
                    aria-current="{{ request()->routeIs('welcome*') ? 'page' : 'false' }}"
                >
                    Home
                </a>

                {{-- Decorative circle --}}
                <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'transition duration-200',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-60 hover:opacity-100' => ! request()->routeIs('early-adopter*'),
                    ])
                    aria-current="{{ request()->routeIs('early-adopter*') ? 'page' : 'false' }}"
                >
                    Mobile
                </a>

                {{-- Decorative circle --}}
                <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
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

                {{-- Decorative circle --}}
                <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{--
                    Link
                    <a
                    href="{{ route('blog') }}"
                    @class([
                    'transition duration-200',
                    'font-medium' => request()->routeIs('blog*'),
                    'opacity-60 hover:opacity-100' => ! request()->routeIs('blog*'),
                    ])
                    aria-current="{{ request()->routeIs('blog*') ? 'page' : 'false' }}"
                    >
                    Blog
                    </a>
                    {{-- Decorative circle -- }}
                    <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200  dark:opacity-60"
                    aria-hidden="true"
                    ></div>
                --}}

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
                    class="relative bg-linear-to-tr from-violet-600 to-violet-300 bg-clip-text font-medium text-transparent dark:from-violet-500 dark:to-white/80"
                    aria-label="Sponsor NativePHP"
                    title="Support NativePHP development"
                >
                    Sponsor
                    <span class="sr-only">NativePHP on GitHub</span>

                    {{-- Heart 1 --}}
                    <div
                        x-ref="sponsorHeart1"
                        class="absolute top-0 right-1/2 origin-center scale-0 opacity-0"
                        aria-hidden="true"
                    >
                        <x-icons.heart class="size-[9px] text-violet-400" />
                    </div>

                    {{-- Heart 2 --}}
                    <div
                        x-ref="sponsorHeart2"
                        class="absolute top-0 left-1/2 origin-center scale-0 opacity-0"
                        aria-hidden="true"
                    >
                        <x-icons.heart class="size-[7px] text-violet-400" />
                    </div>

                    {{-- Heart 3 --}}
                    <div
                        x-ref="sponsorHeart3"
                        class="absolute top-0 right-1/2 origin-center scale-0 opacity-0"
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
                        class="absolute -bottom-1.5 left-0 h-8 w-2 origin-left rounded-full bg-linear-to-t from-violet-500 to-transparent blur-[9px] will-change-transform dark:blur-xs"
                        aria-hidden="true"
                    ></div>
                </a>

                {{-- Theme toggle --}}
                <x-theme-toggle />
            </div>
        </div>
    </div>
</nav>
