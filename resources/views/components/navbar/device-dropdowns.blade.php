<nav
    x-data="{ showDesktopDropdownMenu: false }"
    class="flex items-center gap-5 text-sm"
>
    <div
        x-init="
            () => {
                // Sync Popover ➜ Alpine
                $refs.desktopDropdownPopover.addEventListener('toggle', () => {
                    showDesktopDropdownMenu =
                        $refs.desktopDropdownPopover.matches(':popover-open')
                })

                // Sync Alpine ➜ Popover
                $watch('showDesktopDropdownMenu', (open) => {
                    if (open && ! $refs.desktopDropdownPopover.matches(':popover-open')) {
                        $refs.desktopDropdownPopover.showPopover()
                    } else if (
                        ! open &&
                        $refs.desktopDropdownPopover.matches(':popover-open')
                    ) {
                        $refs.desktopDropdownPopover.hidePopover()
                    }
                })
            }
        "
        class="relative z-0"
    >
        <button
            type="button"
            x-ref="desktopDropdownButton"
            popovertarget="desktopDropdown-menu-popover"
            popovertargetaction="toggle"
            :aria-expanded="showDesktopDropdownMenu"
            aria-controls="desktopDropdown-menu-popover"
            aria-label="Toggle desktopDropdown menu"
            aria-haspopup="true"
            class="flex scale-102 items-center gap-2.5 overflow-hidden rounded-full px-3 py-2 transition duration-200 will-change-transform hover:scale-100 focus:ring-0 focus:outline-none"
            :class="{
                'bg-gray-200/60 hover:bg-gray-200 dark:bg-cloud/50 dark:hover:bg-cloud/70': !showDesktopDropdownMenu,
                'bg-violet-100 dark:bg-violet-400/20': showDesktopDropdownMenu
            }"
        >
            <div class="flex items-center gap-2">
                <x-icons.pc class="size-5 shrink-0" />
                <div>Desktop</div>
            </div>

            <div
                class="transition will-change-transform"
                :class="{
                    'rotate-180': showDesktopDropdownMenu
                }"
            >
                <x-icons.chevron-down class="size-3 text-gray-400" />
            </div>
        </button>

        <div
            popover
            x-anchor.offset.7="$refs.desktopDropdownButton"
            x-ref="desktopDropdownPopover"
            id="desktopDropdown-menu-popover"
            role="dialog"
            aria-modal="true"
            aria-label="Desktop menu"
            class="origin-top -translate-y-2 scale-y-90 overflow-y-scroll overscroll-contain rounded-xl bg-zinc-100/70 opacity-0 ring-1 ring-zinc-200/80 backdrop-blur-2xl transition transition-discrete open:translate-y-0 open:scale-y-100 open:opacity-100 dark:bg-black/50 dark:text-white dark:ring-zinc-700/70 starting:open:-translate-y-2 starting:open:scale-y-0 starting:open:opacity-0"
        >
            <div class="flex h-full flex-col overflow-hidden p-3.5">
                <div class="opacity-70">Menu</div>
                <nav class="mt-2 flex flex-col gap-2">
                    <a
                        href="https://github.com/nativephp"
                        class="flex items-center gap-3 rounded-lg px-3 py-2 transition hover:bg-zinc-200/60"
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
