<nav class="flex items-center gap-5 text-sm">
    <div
        x-data="{
            open: false,
            pointerFine: false,
            closeTimeout: null,
            openMenu(focusFirst = false) {
                if (this.closeTimeout) {
                    clearTimeout(this.closeTimeout)
                    this.closeTimeout = null
                }
                if (this.open) {
                    return
                }
                this.open = true
                this.$nextTick(() => {
                    if (focusFirst && this.$refs.firstItem) {
                        this.$refs.firstItem.focus()
                    }
                })
            },
            closeMenu(refocus = false) {
                if (! this.open) {
                    return
                }
                this.open = false
                this.$nextTick(() => {
                    if (refocus && this.$refs.desktopDropdownButton) {
                        this.$refs.desktopDropdownButton.focus()
                    }
                })
            },
            toggle() {
                this.open ? this.closeMenu(true) : this.openMenu()
            },
        }"
        class="relative z-0"
        x-init="
            (() => {
                const mq = window.matchMedia('(hover: hover) and (pointer: fine)')
                pointerFine = mq.matches
                mq.addEventListener('change', (e) => (pointerFine = e.matches))
            })()
        "
        @mouseenter="pointerFine && openMenu()"
        @mouseleave="pointerFine && (closeTimeout = setTimeout(() => closeMenu(), 200))"
        @click.outside="closeMenu()"
        @keydown.escape.window="closeMenu(true)"
    >
        <button
            type="button"
            x-ref="desktopDropdownButton"
            id="desktopDropdownButton"
            :aria-expanded="open"
            aria-controls="desktopDropdown-menu"
            aria-label="Toggle desktopDropdown menu"
            aria-haspopup="menu"
            class="flex scale-102 items-center gap-2.5 overflow-hidden rounded-full px-3 py-2 transition duration-200 will-change-transform hover:scale-100 focus:ring-0 focus:outline-none"
            :class="{
                'bg-zinc-200/60 hover:bg-zinc-200 dark:bg-cloud/50 dark:hover:bg-cloud/70': !open,
                'bg-zinc-200/70 dark:bg-cloud/80': open
            }"
            @click="toggle()"
            @keydown.enter.prevent="toggle()"
            @keydown.space.prevent="toggle()"
            @keydown.arrow-down.prevent="openMenu(true)"
            @keydown.tab="closeMenu()"
        >
            <div class="flex items-center gap-2">
                <x-icons.pc class="size-5 shrink-0" />
                <div>Desktop</div>
            </div>

            <div
                class="transition duration-200 will-change-transform"
                :class="{
                    'opacity-30': !open,
                    'rotate-180 opacity-50': open
                }"
            >
                <x-icons.chevron-down class="size-3" />
            </div>
        </button>

        <div
            x-cloak
            x-ref="menu"
            x-show="open"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="-translate-y-2 scale-y-90 opacity-0"
            x-transition:enter-end="translate-y-0 scale-y-100 opacity-100"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="translate-y-0 scale-y-100 opacity-100"
            x-transition:leave-end="-translate-y-2 scale-y-90 opacity-0"
            id="desktopDropdown-menu"
            role="menu"
            aria-labelledby="desktopDropdownButton"
            class="absolute top-full right-0 mt-2 w-max max-w-[calc(100vw-1rem)] min-w-[16rem] origin-top overflow-y-auto overscroll-contain rounded-xl bg-white shadow-xl ring-1 shadow-black/5 ring-zinc-200/80 sm:right-auto sm:left-0 dark:bg-black/50 dark:text-white dark:ring-zinc-700/70"
            @mouseenter="pointerFine && (closeTimeout && clearTimeout(closeTimeout))"
            @keydown.tab="closeMenu()"
            @keydown.arrow-down.prevent="(() => {
                const items = Array.from($refs.menu.querySelectorAll('[role=menuitem]'))
                const i = items.indexOf(document.activeElement)
                const next = i === -1 ? 0 : (i + 1) % items.length
                items[next]?.focus()
            })()"
            @keydown.arrow-up.prevent="(() => {
                const items = Array.from($refs.menu.querySelectorAll('[role=menuitem]'))
                const i = items.indexOf(document.activeElement)
                const prev = i === -1 ? items.length - 1 : (i - 1 + items.length) % items.length
                items[prev]?.focus()
            })()"
        >
            <div
                class="flex h-full flex-col overflow-hidden px-3.5 py-4"
                role="none"
            >
                <nav
                    class="flex flex-col gap-2"
                    role="none"
                >
                    <a
                        href="https://github.com/nativephp"
                        class="group hover:bg-snow-flurry-50/70 hover:ring-snow-flurry-100 flex items-center gap-3 rounded-lg py-2 pr-3 pl-2 ring-1 ring-transparent transition"
                        role="menuitem"
                        tabindex="-1"
                        x-ref="firstItem"
                    >
                        {{-- Icon --}}
                        <div
                            class="group-hover:bg-snow-flurry-200/30 group-hover:ring-snow-flurry-200/50 grid size-10 place-items-center rounded-lg bg-zinc-100 ring-1 ring-transparent transition ring-inset"
                        >
                            <x-icons.github
                                class="size-5 transition will-change-transform group-hover:scale-95"
                            />
                        </div>

                        {{-- Right side --}}
                        <div class="relative grow">
                            {{-- Title --}}
                            <div class="font-medium">GitHub</div>

                            {{-- Subtitle --}}
                            <div
                                class="mt-0.5 text-xs opacity-70 group-hover:mask-r-from-0%"
                            >
                                Visit our GitHub repository
                            </div>

                            {{-- Arrow --}}
                            <x-icons.right-arrow
                                class="absolute top-1/2 right-1.5 size-3 -translate-y-1/2 opacity-0 transition will-change-transform group-hover:translate-x-1 group-hover:opacity-100"
                            />
                        </div>
                    </a>
                    <a
                        href="/sponsor"
                        aria-label="Sponsor NativePHP"
                        title="Support NativePHP development"
                        class="group hover:bg-snow-flurry-50/70 hover:ring-snow-flurry-100 flex items-center gap-3 rounded-lg py-2 pr-3 pl-2 ring-1 ring-transparent transition"
                        role="menuitem"
                        tabindex="-1"
                    >
                        {{-- Icon --}}
                        <div
                            class="group-hover:bg-snow-flurry-200/30 group-hover:ring-snow-flurry-200/50 grid size-10 place-items-center rounded-lg bg-zinc-100 ring-1 ring-transparent transition ring-inset"
                        >
                            <x-icons.heart
                                class="size-4 transition will-change-transform group-hover:scale-95"
                            />
                        </div>

                        {{-- Right side --}}
                        <div class="relative grow">
                            {{-- Title --}}
                            <div class="font-medium">Sponsor</div>

                            {{-- Subtitle --}}
                            <div
                                class="mt-0.5 text-xs opacity-70 group-hover:mask-r-from-0%"
                            >
                                Support NativePHP development
                            </div>

                            {{-- Arrow --}}
                            <x-icons.right-arrow
                                class="absolute top-1/2 right-1.5 size-3 -translate-y-1/2 opacity-0 transition will-change-transform group-hover:translate-x-1 group-hover:opacity-100"
                            />
                        </div>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</nav>
