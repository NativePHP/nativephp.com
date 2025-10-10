<div
    x-init="
        () => {
            // Sync Popover ➜ Alpine
            $refs.mobilePopover.addEventListener('toggle', () => {
                showMobileMenu = $refs.mobilePopover.matches(':popover-open')
            })

            // Sync Alpine ➜ Popover
            $watch('showMobileMenu', (open) => {
                if (open && ! $refs.mobilePopover.matches(':popover-open')) {
                    $refs.mobilePopover.showPopover()
                } else if (! open && $refs.mobilePopover.matches(':popover-open')) {
                    $refs.mobilePopover.hidePopover()
                }
            })
        }
    "
    class="relative z-40 lg:hidden"
>
    <button
        type="button"
        popovertarget="mobile-menu-popover"
        popovertargetaction="toggle"
        class="-m-1.5 grid size-9 place-items-center overflow-hidden focus:ring-0 focus:outline-none"
        :aria-expanded="showMobileMenu"
        aria-controls="mobile-menu-popover"
        aria-label="Toggle mobile menu"
        aria-haspopup="true"
        title="Open main navigation"
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
        x-ref="mobilePopover"
        id="mobile-menu-popover"
        role="dialog"
        aria-modal="true"
        aria-label="Site menu"
        class="fixed top-20 right-3 bottom-3.5 left-3 w-auto origin-top -translate-y-2 scale-y-90 overflow-y-scroll overscroll-contain rounded-2xl bg-gray-200/50 opacity-0 ring-1 ring-gray-200/80 backdrop-blur-2xl transition transition-discrete duration-300 open:translate-y-0 open:scale-y-100 open:opacity-100 min-[500px]:right-3.5 min-[500px]:left-3.5 dark:bg-black/50 dark:text-white dark:ring-gray-700/70 starting:open:-translate-y-2 starting:open:scale-y-0 starting:open:opacity-0"
    >
        <div class="flex flex-col overflow-hidden px-6 pt-4 pb-6">
            <nav
                class="grid grid-cols-2 text-xl"
                aria-label="Primary"
            >
                @php
                    $isHomeActive = request()->routeIs('welcome*');
                    $isPricingActive = request()->routeIs('pricing*');
                    $isDocsActive = request()->is('docs*');
                    $isBlogActive = request()->routeIs('blog*');
                    $isPartnersActive = request()->routeIs('partners*');
                    $isSponsorActive = request()->routeIs('sponsoring*');
                    $isLoginActive = request()->routeIs('customer.login*');
                @endphp

                {{-- Home Link --}}
                <div>
                    <a
                        href="/"
                        @class([
                            'flex items-center gap-2 py-3 transition duration-200',
                            'font-medium' => $isHomeActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isHomeActive,
                        ])
                        aria-current="{{ $isHomeActive ? 'page' : 'false' }}"
                    >
                        @if ($isHomeActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif

                        <div>Home</div>
                    </a>
                </div>

                {{-- Docs Link --}}
                <div>
                    <a
                        href="/docs/"
                        @class([
                            'flex items-center gap-2 py-3 transition duration-200',
                            'font-medium' => $isDocsActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isDocsActive,
                        ])
                        aria-current="{{ $isDocsActive ? 'page' : 'false' }}"
                    >
                        @if ($isDocsActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif

                        <div>Docs</div>
                    </a>
                </div>

                {{-- Blog Link --}}
                <div>
                    <a
                        href="{{ route('blog') }}"
                        @class([
                            'flex items-center gap-2 py-3 transition duration-200',
                            'font-medium' => $isBlogActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isBlogActive,
                        ])
                        aria-current="{{ $isBlogActive ? 'page' : 'false' }}"
                    >
                        @if ($isBlogActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif

                        <div>Blog</div>
                    </a>
                </div>

                {{-- Shop Link --}}
                <div>
                    <a
                        href="https://shop.nativephp.com/"
                        class="flex items-center gap-2 py-3 opacity-50 transition duration-200 hover:translate-x-1 hover:opacity-100"
                        aria-label="NativePHP Shop"
                    >
                        <div>Shop</div>
                    </a>
                </div>

                <div>
                    <a
                        href="{{ route('partners') }}"
                        @class([
                            'flex items-center gap-2 py-3 transition duration-200',
                            'font-medium' => $isPartnersActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isPartnersActive,
                        ])
                        aria-current="{{ $isPartnersActive ? 'page' : 'false' }}"
                    >
                        @if ($isPartnersActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif

                        <div>Partners</div>
                    </a>
                </div>

                {{-- Login/Logout --}}
                @feature(App\Features\ShowAuthButtons::class)
                    <div>
                        @auth
                            <form
                                method="POST"
                                action="{{ route('customer.logout') }}"
                                class="w-full"
                            >
                                @csrf
                                <button
                                    type="submit"
                                    class="flex w-full items-center justify-between py-3 opacity-50 transition duration-200 hover:translate-x-1 hover:opacity-100"
                                >
                                    <div>Log out</div>
                                </button>
                            </form>
                        @else
                            <a
                                href="{{ route('customer.login') }}"
                                @class([
                                    'flex items-center gap-2 py-3 transition duration-200',
                                    'font-medium' => $isLoginActive,
                                    'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isLoginActive,
                                ])
                                aria-current="{{ $isLoginActive ? 'page' : 'false' }}"
                            >
                                @if ($isLoginActive)
                                    <x-icons.right-arrow
                                        class="size-4 shrink-0"
                                        aria-hidden="true"
                                        focusable="false"
                                    />
                                @endif

                                <div>Log in</div>
                            </a>
                        @endauth
                    </div>
                @endfeature
            </nav>

            <div
                class="mt-6 mb-2 flex w-full items-center justify-between gap-2 pb-2"
            >
                {{-- Doc search --}}
                <div
                    class="transition-all duration-200 ease-in-out will-change-transform"
                >
                    <div
                        id="docsearch-mobile"
                        x-on:click="
                            window.scrollTo({ top: 0, behavior: 'instant' })
                            showMobileMenu = false
                        "
                        aria-label="Search documentation"
                    ></div>
                </div>

                <div
                    class="flex h-10 items-center gap-0.5 rounded-full bg-gray-100 p-1 text-sm ring-1 ring-black/5 dark:bg-black/20 dark:ring-white/10"
                    role="radiogroup"
                    aria-label="Theme preference"
                >
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="themePreference === 'light'"
                        x-on:click="themePreference = 'light'; showMobileMenu = false"
                        class="rounded-full px-3 py-1.5 transition duration-300 ease-in-out"
                        :class="{
                            'bg-zinc-300/70': themePreference === 'light',
                        }"
                        title="Use light theme"
                    >
                        Light
                    </button>
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="themePreference === 'system'"
                        x-on:click="themePreference = 'system'; showMobileMenu = false"
                        class="rounded-full px-3 py-1.5 transition duration-300 ease-in-out"
                        :class="{
                            'bg-zinc-300/50 dark:bg-gray-200/10': themePreference === 'system',
                        }"
                        title="Use system theme"
                    >
                        System
                    </button>
                    <button
                        type="button"
                        role="radio"
                        :aria-checked="themePreference === 'dark'"
                        x-on:click="themePreference = 'dark'; showMobileMenu = false"
                        class="rounded-full px-3 py-1.5 transition duration-300 ease-in-out"
                        :class="{
                            'bg-gray-200/10': themePreference === 'dark',
                        }"
                        title="Use dark theme"
                    >
                        Dark
                    </button>
                </div>
            </div>

            <div
                class="h-0.5 w-full rounded-full bg-current opacity-5"
                role="presentation"
            ></div>

            <nav
                class="mx-auto mt-4 flex"
                aria-label="Social media"
            >
                <div class="grid grid-cols-4 justify-items-center gap-4">
                    <x-social-networks-all />
                </div>
            </nav>
        </div>
    </div>
</div>
