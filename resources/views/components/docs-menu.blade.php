<div class="lg:hidden">
    <div
        class="mb-2 flex flex-col-reverse items-start justify-between gap-3.5 min-[400px]:mb-2.5 min-[400px]:flex-row min-[400px]:items-center"
    >
        {{-- Docs menu button --}}
        <button
            type="button"
            x-on:click="showDocsMenu = !showDocsMenu"
            class="-mx-1 flex items-center gap-1.5 px-1 py-1.5 transition duration-300 ease-in-out will-change-transform focus:ring-0 focus:outline-none"
            :aria-expanded="showDocsMenu"
            aria-label="Toggle docs menu"
            aria-haspopup="true"
            title="Open docs navigation"
            :class="{
                'translate-x-1': showDocsMenu,
            }"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-5.5"
                viewBox="0 0 24 24"
            >
                <g
                    fill="none"
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-width="1.5"
                >
                    <path
                        d="M4 19V5a2 2 0 0 1 2-2h13.4a.6.6 0 0 1 .6.6v13.114"
                    />
                    <path
                        stroke-linejoin="round"
                        d="M8 3v8l2.5-1.6L13 11V3"
                    />
                    <path d="M6 17h14M6 21h14" />
                    <path
                        stroke-linejoin="round"
                        d="M6 21a2 2 0 1 1 0-4"
                    />
                </g>
            </svg>

            <div>Menu</div>
        </button>

        {{-- Platform switcher --}}
        <x-mini-platform-switcher />
    </div>

    {{-- Docs mobile menu --}}
    <div
        x-show="showDocsMenu"
        x-collapse
        role="dialog"
        aria-modal="true"
        aria-label="Docs menu"
        class="dark:bg-mirage rounded-xl bg-gray-100"
    >
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
