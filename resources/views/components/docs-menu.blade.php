<div class="lg:hidden">
    <div
        class="mb-3 flex flex-col-reverse items-start justify-between gap-5 min-[400px]:flex-row min-[400px]:items-center"
    >
        {{-- Docs menu button --}}
        <button
            type="button"
            x-on:click="showDocsMenu = !showDocsMenu"
            class="flex items-center gap-0.5 focus:ring-0 focus:outline-none"
            :aria-expanded="showDocsMenu"
            aria-label="Toggle docs menu"
            aria-haspopup="true"
            title="Open docs navigation"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-5 shrink-0 transition duration-300 will-change-transform"
                :class="{
                        'rotate-90': showDocsMenu,
                    }"
                aria-hidden="true"
                focusable="false"
                viewBox="0 0 24 24"
            >
                <g
                    fill="none"
                    fill-rule="evenodd"
                >
                    <path
                        d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.019-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"
                    />
                    <path
                        fill="currentColor"
                        d="M15.707 11.293a1 1 0 0 1 0 1.414l-5.657 5.657a1 1 0 1 1-1.414-1.414l4.95-4.95l-4.95-4.95a1 1 0 0 1 1.414-1.414z"
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
        <div class="relative p-6">
            {{ $slot }}
        </div>
    </div>
</div>
