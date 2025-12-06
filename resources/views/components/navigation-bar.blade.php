<nav
    class="sticky top-0 z-50 flex flex-col items-center justify-center border-b px-2 transition duration-200 ease-out min-[500px]:px-3"
    aria-label="Main Navigation"
    :class="{
        'border-b-gray-200/80 backdrop-blur-2xl dark:border-b-gray-700/70 bg-white/50 dark:bg-black/50': scrolled || showMobileMenu,
        'border-b-transparent dark:bg-transparent': ! scrolled && ! showMobileMenu,
    }"
>
    <div
        class="mx-auto flex w-full max-w-5xl items-center justify-between gap-5 rounded-2xl py-4 pr-2 pl-2 3xs:pr-4 3xs:pl-3.5 xl:max-w-7xl 2xl:max-w-360"
    >
        {{-- Left side --}}
        <div class="flex items-center gap-2.5">
            {{-- Logo --}}
            <a
                href="/"
                aria-label="NativePHP Homepage"
                x-on:contextmenu.prevent="window.location.href = @js(route('brand'))"
                class="hidden 3xs:block"
            >
                <x-logo
                    class="hidden h-4 min-[400px]:h-5 min-[500px]:block sm:h-6"
                />
                <x-mini-logo class="block h-6 min-[500px]:hidden" />
                <span class="sr-only">NativePHP</span>
            </a>

            <x-navbar.device-dropdowns />

            {{-- 👇 Temporarily kept in case of a future announcement --}}
            {{-- V1 Announcement --}}
            {{--
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
            --}}
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-2.5">
            {{-- Mobile menu --}}
            <x-navbar.mobile-menu />

            {{-- Desktop menu --}}
            <div
                class="hidden items-center gap-2.5 text-sm lg:flex"
                aria-label="Primary navigation"
            >
                {{-- Link --}}
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

                {{-- Decorative circle --}}
                <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="https://shop.nativephp.com/"
                    class="opacity-60 transition duration-200 hover:opacity-100"
                >
                    Shop
                </a>

                {{-- Decorative circle --}}
                <div
                    class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                    aria-hidden="true"
                ></div>

                {{-- Link --}}
                <a
                    href="/partners"
                    class="opacity-60 transition duration-200 hover:opacity-100"
                >
                    Partners
                </a>

                {{-- Login/Logout --}}
                @feature(App\Features\ShowAuthButtons::class)
                    {{-- Decorative circle --}}
                    <div
                        class="size-[3px] rotate-45 rounded-xs bg-gray-400 transition duration-200 dark:opacity-60"
                        aria-hidden="true"
                    ></div>

                    @auth
                        <form
                            method="POST"
                            action="{{ route('customer.logout') }}"
                            class="inline"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="opacity-60 transition duration-200 hover:opacity-100"
                            >
                                Log out
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('customer.login') }}"
                            class="opacity-60 transition duration-200 hover:opacity-100"
                        >
                            Log in
                        </a>
                    @endauth
                @endfeature

                {{-- Theme toggle --}}
                <x-navbar.theme-toggle />

                {{-- Doc search --}}
                <div
                    class="-mr-0.5 transition-all duration-200 ease-in-out will-change-transform"
                >
                    <div
                        id="docsearch-desktop"
                        x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
                        aria-label="Search documentation"
                    ></div>
                </div>

                <x-bifrost-button small />
            </div>
        </div>
    </div>
</nav>
