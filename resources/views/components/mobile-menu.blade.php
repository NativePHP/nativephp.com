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
        class="fixed top-23 right-3.5 bottom-3.5 left-3.5 h-auto w-auto origin-top -translate-y-2 scale-y-90 overflow-y-scroll overscroll-contain rounded-2xl bg-gray-100/50 opacity-0 ring-1 ring-gray-200/80 backdrop-blur-2xl transition transition-discrete duration-300 open:translate-y-0 open:scale-y-100 open:opacity-100 dark:bg-black/50 dark:text-white dark:ring-gray-700/70 starting:open:-translate-y-2 starting:open:scale-y-0 starting:open:opacity-0"
    >
        <div
            x-data="{
                mobileMenuTimeline: null,

                init() {
                    mobileMenuTimeline = gsap
                        .timeline({
                            paused: true,
                            delay: 0.2,
                        })
                        .fromTo(
                            '.gsap-mobile-menu-link',
                            {
                                y: 30,
                                autoAlpha: 0,
                                scale: 1.1,
                            },
                            {
                                y: 0,
                                autoAlpha: 1,
                                scale: 1,
                                ease: 'circ.out',
                                stagger: 0.05,
                                duration: 0.5,
                            },
                        )
                        .fromTo(
                            '.gsap-mobile-menu-divider',
                            {
                                x: -10,
                                autoAlpha: 0,
                            },
                            {
                                x: 0,
                                autoAlpha: 0.05,
                                ease: 'circ.out',
                                stagger: 0.1,
                                duration: 0.5,
                            },
                            '0',
                        )
                        .fromTo(
                            '.gsap-mobile-menu-left-to-right-slide',
                            {
                                x: -30,
                                y: -30,
                                scale: 1.1,
                                autoAlpha: 0,
                            },
                            {
                                x: 0,
                                y: 0,
                                scale: 1,
                                autoAlpha: 1,
                                ease: 'circ.out',
                                duration: 0.5,
                            },
                            '0',
                        )
                        .fromTo(
                            '.gsap-mobile-menu-right-to-left-slide',
                            {
                                x: 30,
                                y: -30,
                                scale: 1.1,
                                autoAlpha: 0,
                            },
                            {
                                x: 0,
                                y: 0,
                                scale: 1,
                                autoAlpha: 1,
                                ease: 'circ.out',
                                duration: 0.5,
                            },
                            '0',
                        )
                        .fromTo(
                            '.gsap-mobile-menu-social-media > *',
                            {
                                y: 30,
                                autoAlpha: 0,
                            },
                            {
                                y: 0,
                                autoAlpha: 1,
                                ease: 'circ.out',
                                duration: 0.5,
                                stagger: 0.05,
                            },
                            '0',
                        )

                    $watch('showMobileMenu', (value) => {
                        if (value) {
                            mobileMenuTimeline.restart(true, false)
                        }
                    })
                },
            }"
            class="flex h-full flex-col overflow-hidden p-6"
        >
            <nav
                class="flex flex-1 flex-col items-start text-xl"
                aria-label="Primary"
            >
                @php
                    $isHomeActive = request()->routeIs('welcome*');
                    $isMobileActive = request()->routeIs('early-adopter*');
                    $isDocsActive = request()->is('docs*');
                    $isSponsorActive = request()->is('docs/*/getting‐started/sponsoring*');
                @endphp

                {{-- Home Link --}}
                <div class="gsap-mobile-menu-link w-full">
                    <a
                        href="/"
                        @class([
                            'flex items-center justify-between py-3 transition duration-200',
                            'font-medium' => $isHomeActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isHomeActive,
                        ])
                        aria-current="{{ $isHomeActive ? 'page' : 'false' }}"
                    >
                        <div>Home</div>
                        @if ($isHomeActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif
                    </a>
                </div>

                <div
                    class="gsap-mobile-menu-divider h-0.5 w-full rounded-full bg-current opacity-5"
                    role="presentation"
                ></div>

                {{-- Mobile Link --}}
                <div class="gsap-mobile-menu-link w-full">
                    <a
                        href="{{ route('early-adopter') }}"
                        @class([
                            'flex items-center justify-between py-3 transition duration-200',
                            'font-medium' => $isMobileActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isMobileActive,
                        ])
                        aria-current="{{ $isMobileActive ? 'page' : 'false' }}"
                    >
                        <div>Mobile</div>
                        @if ($isMobileActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif
                    </a>
                </div>

                <div
                    class="gsap-mobile-menu-divider h-0.5 w-full rounded-full bg-current opacity-5"
                    role="presentation"
                ></div>

                {{-- Docs Link --}}
                <div class="gsap-mobile-menu-link w-full">
                    <a
                        href="/docs/"
                        @class([
                            'flex items-center justify-between py-3 transition duration-200',
                            'font-medium' => $isDocsActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isDocsActive,
                        ])
                        aria-current="{{ $isDocsActive ? 'page' : 'false' }}"
                    >
                        <div>Docs</div>
                        @if ($isDocsActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif
                    </a>
                </div>

                <div
                    class="gsap-mobile-menu-divider h-0.5 w-full rounded-full bg-current opacity-5"
                    role="presentation"
                ></div>

                {{-- Sponsor Link --}}
                <div class="gsap-mobile-menu-link w-full">
                    <a
                        href="/docs/1/getting‐started/sponsoring"
                        @class([
                            'flex items-center justify-between py-3 transition duration-200',
                            'font-medium' => $isSponsorActive,
                            'opacity-50 hover:translate-x-1 hover:opacity-100' => ! $isSponsorActive,
                        ])
                        aria-label="Sponsor NativePHP project"
                        aria-current="{{ $isSponsorActive ? 'page' : 'false' }}"
                    >
                        <div>Sponsor</div>
                        @if ($isSponsorActive)
                            <x-icons.right-arrow
                                class="size-4 shrink-0"
                                aria-hidden="true"
                                focusable="false"
                            />
                        @endif
                    </a>
                </div>
            </nav>

            <div
                class="mb-2 flex w-full items-center justify-between gap-2 pb-2"
            >
                <div class="gsap-mobile-menu-left-to-right-slide">Theme:</div>
                <button
                    x-on:click="darkMode = !darkMode; showMobileMenu = false"
                    class="gsap-mobile-menu-right-to-left-slide flex h-10 items-center gap-0.5 rounded-full bg-gray-100 p-1 ring-1 ring-black/5 dark:bg-black/20 dark:ring-white/10"
                    aria-pressed="false"
                    aria-label="Toggle light or dark theme"
                    title="Switch between dark and light mode"
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

            <div
                class="gsap-mobile-menu-divider h-0.5 w-full rounded-full bg-current opacity-5"
                role="presentation"
            ></div>

            <nav
                class="gsap-mobile-menu-social-media mt-4 flex flex-wrap items-center justify-center gap-2.5"
                aria-label="Social media"
            >
                <x-social-networks-all />
            </nav>
        </div>
    </div>
</div>
