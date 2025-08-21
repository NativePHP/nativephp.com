@php
    /**
     * Props:
     * - string $label     The dropdown button label (e.g., "Desktop", "Mobile").
     * - string $icon      The icon component name from x-icons.* (e.g., 'pc', 'tablet-smartphone').
     * - ?string $id       Optional unique id base to avoid collisions across multiple dropdowns.
     */
    $label = $label ?? 'Devices';
    $icon = $icon ?? 'pc';
    $base = $id ?? \Illuminate\Support\Str::slug($label) . '-' . \Illuminate\Support\Str::random(6);
    $buttonId = $base . '-btn';
    $menuId = $base . '-menu';
@endphp

<div
    x-data="{
        id: '{{ $base }}',
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
            window.dispatchEvent(
                new CustomEvent('navbar:dropdown:open', { detail: this.id }),
            )
            this.open = true
            this.$nextTick(() => {
                if (focusFirst && this.$refs.dropdownItemsNav) {
                    const firstItem =
                        this.$refs.dropdownItemsNav.querySelector('[role=menuitem]')
                    if (firstItem) {
                        firstItem.focus()
                    }
                }
            })
        },
        closeMenu(refocus = false) {
            if (! this.open) {
                return
            }
            this.open = false
            this.$nextTick(() => {
                if (refocus && this.$refs.dropdownButton) {
                    this.$refs.dropdownButton.focus()
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

            // Ensure only one dropdown remains open at any time
            window.addEventListener('navbar:dropdown:open', (e) => {
                if (e.detail !== id) {
                    closeMenu()
                }
            })
        })()
    "
    @mouseenter="pointerFine && openMenu()"
    @mouseleave="pointerFine && (closeTimeout = setTimeout(() => closeMenu(), 200))"
    @click.outside="closeMenu()"
    @keydown.escape.window="closeMenu(true)"
>
    <button
        type="button"
        x-ref="dropdownButton"
        id="{{ $buttonId }}"
        :aria-expanded="open"
        aria-controls="{{ $menuId }}"
        aria-label="Toggle {{ $label }} menu"
        aria-haspopup="menu"
        class="flex items-center gap-2 overflow-hidden rounded-full px-3 py-2 text-xs transition duration-200 select-none focus:ring-0 focus:outline-none lg:text-sm"
        :class="{
            'bg-zinc-200/60 hover:bg-zinc-200 dark:bg-cloud/45 dark:hover:bg-cloud/70': !open,
            'bg-violet-300/70 dark:bg-cloud': open
        }"
        @click="toggle()"
        @keydown.enter.prevent="toggle()"
        @keydown.space.prevent="toggle()"
        @keydown.arrow-down.prevent="openMenu(true)"
        @keydown.tab="closeMenu()"
    >
        <div class="flex items-center gap-2">
            <x-dynamic-component
                :component="'icons.' . $icon"
                class="h-4.5 shrink-0 lg:h-5"
            />
            <div>{{ $label }}</div>
        </div>

        <div
            class="transition duration-200 will-change-transform"
            :class="{
                'opacity-30': !open,
                'rotate-180 opacity-50': open
            }"
        >
            <x-icons.chevron-down class="size-2.5" />
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
        id="{{ $menuId }}"
        role="menu"
        aria-labelledby="{{ $buttonId }}"
        class="dark:bg-mirage absolute top-full left-0 mt-2 w-max max-w-[calc(100vw-1rem)] min-w-[16rem] origin-top overflow-y-auto overscroll-contain rounded-xl bg-white shadow-xl ring-1 shadow-black/5 ring-zinc-200/80 sm:right-auto sm:left-0 dark:text-white dark:ring-zinc-700/70"
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
                x-ref="dropdownItemsNav"
                class="flex flex-col gap-2 text-sm"
                role="none"
            >
                {{ $slot }}
            </nav>
        </div>
    </div>
</div>
