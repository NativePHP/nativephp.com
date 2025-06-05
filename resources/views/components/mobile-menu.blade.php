<div class="relative z-40 lg:hidden">
    <button
        type="button"
        popovertarget="mobile-menu-popover"
        x-on:click="showMobileMenu = !showMobileMenu"
        class="-m-1.5 grid size-9 place-items-center overflow-hidden focus:ring-0 focus:outline-none"
        aria-expanded="false"
        aria-controls="mobile-navigation"
        aria-label="Toggle mobile menu"
    >
        <div class="flex flex-col items-end gap-1 [grid-area:1/-1]">
            <div
                class="h-0.5 w-5 rounded-full bg-current transition duration-300 ease-in-out"
                :class="{'translate-x-2 opacity-0': showMobileMenu}"
            ></div>
            <div
                class="h-0.5 w-5 rounded-full bg-current transition delay-50 duration-300 ease-in-out"
                :class="{'translate-x-2 opacity-0': showMobileMenu}"
            ></div>
            <div
                class="h-0.5 w-3.5 rounded-full bg-current transition delay-100 duration-300 ease-in-out"
                :class="{'translate-x-2 opacity-0': showMobileMenu}"
            ></div>
        </div>
        <div class="flex items-center gap-2 [grid-area:1/-1]">
            <div
                class="h-5 w-0.5 rotate-45 rounded-full bg-current transition duration-300 ease-in-out"
                :class="{
                    'scale-y-100': showMobileMenu,
                    'opacity-0 scale-y-0 -translate-y-4 translate-x-4': !showMobileMenu
                }"
            ></div>
            <div
                class="-ml-2.5 h-5 w-0.5 -rotate-45 rounded-full bg-current transition duration-300 ease-in-out"
                :class="{
                    'scale-y-100': showMobileMenu,
                    'opacity-0 scale-y-0 -translate-y-4 -translate-x-4': !showMobileMenu
                }"
            ></div>
        </div>
    </button>

    <div
        popover
        id="mobile-menu-popover"
        x-on:click.outside="showMobileMenu = false"
        x-on:keydown.escape.window="showMobileMenu = false"
        class="fixed top-23 right-3.5 bottom-3.5 left-3.5 h-auto w-auto origin-top -translate-y-2 scale-y-90 overflow-y-scroll overscroll-contain rounded-2xl bg-gray-100/50 text-xl opacity-0 ring-1 ring-gray-200/80 backdrop-blur-2xl transition transition-discrete duration-300 open:translate-y-0 open:scale-y-100 open:opacity-100 dark:bg-black/50 dark:text-white dark:ring-gray-700/70 starting:open:-translate-y-2 starting:open:scale-y-0 starting:open:opacity-0"
    >
        <div class="flex h-full flex-col p-6">
            <nav class="flex flex-1 flex-col items-start">
                {{-- Link --}}
                <a
                    href="/"
                    @class([
                        'flex w-full items-center justify-between py-3 transition duration-200',
                        'font-medium' => request()->routeIs('welcome*'),
                        'opacity-50 hover:translate-x-1 hover:opacity-100' => ! request()->routeIs(
                            'welcome*',
                        ),
                    ])
                    aria-current="{{ request()->routeIs('welcome*') ? 'page' : 'false' }}"
                >
                    <div>Home</div>

                    @if (request()->routeIs('welcome*'))
                        <x-icons.right-arrow
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                    @endif
                </a>

                {{-- Divider --}}
                <div
                    class="h-0.5 w-full rounded-full bg-current opacity-5"
                ></div>

                {{-- Link --}}
                <a
                    href="{{ route('early-adopter') }}"
                    @class([
                        'flex w-full items-center justify-between py-3 transition duration-200',
                        'font-medium' => request()->routeIs('early-adopter*'),
                        'opacity-50 hover:translate-x-1 hover:opacity-100' => ! request()->routeIs(
                            'early-adopter*',
                        ),
                    ])
                    aria-current="{{ request()->routeIs('early-adopter*') ? 'page' : 'false' }}"
                >
                    <div>Mobile</div>

                    @if (request()->routeIs('early-adopter*'))
                        <x-icons.right-arrow
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                    @endif
                </a>

                {{-- Divider --}}
                <div
                    class="h-0.5 w-full rounded-full bg-current opacity-5"
                ></div>

                {{-- Link --}}
                <a
                    href="/docs/"
                    @class([
                        'flex w-full items-center justify-between py-3 transition duration-200',
                        'font-medium' => request()->is('docs*'),
                        'opacity-50 hover:translate-x-1 hover:opacity-100' => ! request()->is(
                            'docs*',
                        ),
                    ])
                    aria-current="{{ request()->is('docs*') ? 'page' : 'false' }}"
                >
                    <div>Docs</div>

                    @if (request()->routeIs('docs*'))
                        <x-icons.right-arrow
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                    @endif
                </a>

                {{-- Divider --}}
                <div
                    class="h-0.5 w-full rounded-full bg-current opacity-5"
                ></div>

                {{-- Link --}}
                <a
                    href="/docs/1/getting-started/sponsoring"
                    @class([
                        'flex w-full items-center justify-between py-3 transition duration-200',
                        'font-medium' => request()->is('docs/*/getting-started/sponsoring'),
                        'opacity-50 hover:translate-x-1 hover:opacity-100' => ! request()->is(
                            'docs/*/getting-started/sponsoring',
                        ),
                    ])
                    aria-label="Sponsor NativePHP"
                    title="Support NativePHP development"
                    aria-current="{{ request()->is('docs/*/getting-started/sponsoring') ? 'page' : 'false' }}"
                >
                    <div>Sponsor</div>

                    @if (request()->is('docs/*/getting-started/sponsoring'))
                        <x-icons.right-arrow
                            class="size-4 shrink-0"
                            aria-hidden="true"
                        />
                    @endif
                </a>
            </nav>

            {{-- Theme toggle --}}
            <div
                class="mb-2 flex w-full items-center justify-between gap-2 pb-2 text-sm"
            >
                {{-- Label --}}
                <div class="">Theme:</div>
                {{-- Theme toggle --}}
                <button
                    x-on:click="darkMode = !darkMode"
                    class="flex h-10 items-center gap-0.5 rounded-full bg-gray-100 p-1 ring-1 ring-black/5 dark:bg-black/20 dark:ring-white/10"
                >
                    <div
                        class="rounded-full px-4 py-1.5 transition duration-300 ease-in-out"
                        :class="{
                            'bg-gray-200/10': darkMode,
                        }"
                    >
                        Dark
                    </div>
                    <div
                        class="rounded-full px-4 py-1.5 transition duration-300 ease-in-out"
                        :class="{
                            'bg-zinc-300/70': !darkMode,
                        }"
                    >
                        Light
                    </div>
                </button>
            </div>

            {{-- Divider --}}
            <div class="h-0.5 w-full rounded-full bg-current opacity-5"></div>

            {{-- Social links --}}
            <nav
                class="mt-4 flex flex-wrap items-center justify-center gap-2.5"
                aria-label="Social networks"
            >
                <x-social-networks-all />
            </nav>
        </div>
    </div>
</div>
