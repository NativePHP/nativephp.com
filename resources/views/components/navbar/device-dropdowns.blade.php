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
                'bg-gray-200/60 hover:bg-gray-200 dark:bg-cloud/50 dark:hover:bg-cloud/70': !open,
                'bg-violet-100 dark:bg-violet-400/20': open
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
                class="transition will-change-transform"
                :class="{
                    'rotate-180': open
                }"
            >
                <x-icons.chevron-down class="size-3 text-gray-400" />
            </div>
        </button>

        <div
            x-cloak
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
            class="absolute top-full left-0 mt-2 w-max max-w-[calc(100vw-2rem)] min-w-[16rem] origin-top overflow-y-scroll overscroll-contain rounded-xl bg-zinc-100/70 ring-1 ring-zinc-200/80 backdrop-blur-2xl dark:bg-black/50 dark:text-white dark:ring-zinc-700/70"
            x-ref="menu"
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
                class="flex h-full flex-col overflow-hidden p-3.5"
                role="none"
            >
                <div class="opacity-70">Menu</div>
                <nav
                    class="mt-2 flex flex-col gap-2"
                    role="none"
                >
                    <a
                        href="https://github.com/nativephp"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-zinc-200/60"
                        role="menuitem"
                        tabindex="-1"
                        x-ref="firstItem"
                    >
                        {{-- Icon --}}
                        <div
                            class="grid size-11 place-items-center rounded-lg bg-white"
                        >
                            <x-icons.github class="size-6" />
                        </div>

                        {{-- Right side --}}
                        <div>
                            {{-- Title --}}
                            <div class="font-medium">GitHub</div>

                            {{-- Subtitle --}}
                            <div class="text-xs opacity-70">
                                Visit our GitHub repository
                            </div>
                        </div>
                    </a>
                    <a
                        href="/sponsor"
                        aria-label="Sponsor NativePHP"
                        title="Support NativePHP development"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-zinc-200/60"
                        role="menuitem"
                        tabindex="-1"
                    >
                        {{-- Icon --}}
                        <div
                            class="grid size-11 place-items-center rounded-lg bg-white"
                        >
                            <x-icons.heart class="size-5" />
                        </div>

                        {{-- Right side --}}
                        <div>
                            {{-- Title --}}
                            <div class="font-medium">Sponsor</div>

                            {{-- Subtitle --}}
                            <div class="text-xs opacity-70">
                                Support NativePHP development
                            </div>
                        </div>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</nav>
