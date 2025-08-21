<div
    x-on:click="
        // Cycle preference: light -> dark -> system -> light
        themePreference =
            themePreference === 'light'
                ? 'dark'
                : themePreference === 'dark'
                  ? 'system'
                  : 'light'
    "
    x-data="{
        timeline: null,
        label() {
            return this.themePreference === 'system'
                ? 'Use system theme'
                : this.themePreference === 'dark'
                  ? 'Use dark theme'
                  : 'Use light theme'
        },

        readable(pref) {
            if (pref === 'system') {
                return 'Auto'
            }

            if (pref === 'dark') {
                return 'Dark'
            }

            return 'Light'
        },

        nextPref(pref) {
            // Cycle: light -> dark -> system -> light
            if (pref === 'light') {
                return 'dark'
            }

            if (pref === 'dark') {
                return 'system'
            }

            return 'light'
        },

        hintText() {
            return `${this.readable(this.themePreference)} → ${this.readable(this.nextPref(this.themePreference))}`
        },

        init() {
            gsap.to('.theme-selector', { scale: 1, duration: 0.1 })

            timeline = gsap
                .timeline({ paused: true })
                .to('.theme-selector-moon', {
                    rotate: 70,
                    ease: 'sine.out',
                    duration: 0.3,
                })
                .to(
                    '.theme-selector-mini-star',
                    { autoAlpha: 0, scale: 0, ease: 'sine.out', duration: 0.3 },
                    '>-0.3',
                )
                .to(
                    '.theme-selector-micro-star',
                    { autoAlpha: 0, scale: 0, ease: 'sine.out', duration: 0.3 },
                    '<',
                )
                .to(
                    '.theme-selector-moon',
                    { scale: 0.6, ease: 'sine.out', duration: 0.3 },
                    '<',
                )
                .fromTo(
                    '.theme-selector-sunball',
                    { scale: 0, x: -5, y: 5 },
                    { x: 0, y: 0, scale: 1, ease: 'expo.out', duration: 0.3 },
                    '>-0.15',
                )
                .fromTo(
                    '.theme-selector-sunshine',
                    { scale: 0, rotate: -180 },
                    { scale: 1, rotate: 0, ease: 'expo.out', duration: 0.3 },
                    '<',
                )

            // Jump to correct position based on effective theme
            if (isDark) {
                timeline.progress(1)
            } else {
                timeline.progress(0)
            }

            $watch('isDark', (value) => {
                if (value) {
                    timeline.play()
                } else {
                    timeline.reverse()
                }
            })

            // Ensure animation also plays when coming from 'system' -> explicit light/dark
            this.$watch('themePreference', (val, prev) => {
                if (prev === 'system' && (val === 'light' || val === 'dark')) {
                    this.$nextTick(() => {
                        if (val === 'light') {
                            // Start from dark end and animate to light
                            timeline.progress(1).pause()
                            gsap.to(timeline, {
                                progress: 0,
                                duration: 0.4,
                                ease: 'power2.out',
                            })
                        } else if (val === 'dark') {
                            // Start from light start and animate to dark
                            timeline.progress(0).pause()
                            gsap.to(timeline, {
                                progress: 1,
                                duration: 0.4,
                                ease: 'power2.out',
                            })
                        }
                    })
                }
            })
        },
    }"
    role="button"
    aria-label="Toggle theme"
    x-bind:aria-pressed="themePreference !== 'system' ? 'true' : 'mixed'"
    tabindex="0"
    class="theme-selector group relative -mx-1.5 size-9 cursor-pointer transition duration-300 select-none hover:opacity-75 dark:opacity-70 dark:hover:opacity-100"
>
    <span
        class="sr-only"
        x-text="label()"
    >
        Toggle theme
    </span>

    {{-- System icon --}}
    <div
        x-show="themePreference === 'system'"
        class="absolute inset-0 grid place-items-center"
        aria-hidden="true"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 48 48"
            class="size-5"
            aria-hidden="true"
            focusable="false"
        >
            <path
                fill="currentColor"
                d="M22.3 25.75c1.6667 1.6667 3.4917 3.1 5.475 4.3 1.9833 1.2 4.125 2.1333 6.425 2.8 -1.2667 1.5 -2.7833 2.65 -4.55 3.45 -1.7667 0.8 -3.6333 1.2 -5.6 1.2 -3.7333 0 -6.9167 -1.3167 -9.55 -3.95 -2.6333 -2.6333 -3.95 -5.8167 -3.95 -9.55 0 -1.9667 0.4 -3.8333 1.2 -5.6 0.8 -1.7667 1.95 -3.2833 3.45 -4.55 0.6667 2.3 1.6 4.4417 2.8 6.425 1.2 1.9833 2.6333 3.8083 4.3 5.475Zm13.7 4.5c-0.5 -0.1333 -1 -0.275 -1.5 -0.425 -0.5 -0.15 -0.9833 -0.325 -1.45 -0.525 0.4667 -0.8 0.825 -1.65 1.075 -2.55 0.25 -0.9 0.375 -1.8167 0.375 -2.75 0 -2.9333 -1.0167 -5.4167 -3.05 -7.45 -2.0333 -2.0333 -4.5167 -3.05 -7.45 -3.05 -0.9333 0 -1.85 0.125 -2.75 0.375 -0.9 0.25 -1.75 0.6083 -2.55 1.075 -0.1667 -0.4667 -0.325 -0.9417 -0.475 -1.425 -0.15 -0.4833 -0.2917 -0.975 -0.425 -1.475 0.9667 -0.5 1.975 -0.8833 3.025 -1.15 1.05 -0.2667 2.125 -0.4 3.225 -0.4 3.7333 0 6.9167 1.3167 9.55 3.95 2.6333 2.6333 3.95 5.8167 3.95 9.55 0 1.1 -0.1333 2.175 -0.4 3.225 -0.2667 1.05 -0.65 2.0583 -1.15 3.025ZM22.5 6V0h3v6h-3Zm0 42v-6h3v6h-3Zm15.3 -35.65 -2.15 -2.15L39.9 6l2.15 2.1 -4.25 4.25ZM8.1 42l-2.15 -2.1 4.25 -4.25 2.15 2.15L8.1 42ZM42 25.5v-3h6v3h-6Zm-42 0v-3h6v3H0Zm39.9 16.55 -4.25 -4.25 2.15 -2.15L42 39.9l-2.1 2.15Zm-29.7 -29.7L6 8.1l2.1 -2.15 4.25 4.25 -2.15 2.15Z"
                stroke-width="1"
            ></path>
        </svg>
    </div>
    {{-- Animated moon/sun group (hidden in system mode) --}}
    <div
        x-show="themePreference !== 'system'"
        class="absolute inset-0"
        aria-hidden="true"
    >
        {{-- Moon --}}
        <div
            class="theme-selector-moon absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2"
        >
            <div class="-scale-x-100 transition duration-300">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-[20px]"
                    viewBox="0 0 16 16"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        fill="currentColor"
                        d="M6 .278a.768.768 0 0 1 .08.858a7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277c.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316a.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71C0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
                    />
                </svg>
            </div>
        </div>
        {{-- Mini star --}}
        <div class="theme-selector-mini-star absolute top-[.5rem] left-[.5rem]">
            <div class="transition duration-300">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-[7px]"
                    viewBox="0 0 256 256"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        fill="currentColor"
                        d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0L89.93 166L26.5 143a16 16 0 0 1 0-30L90 89.93l23-63.43a16 16 0 0 1 30 0L166.07 90l63.43 23a15.79 15.79 0 0 1 10.5 15Z"
                    />
                </svg>
            </div>
        </div>
        {{-- Micro star --}}
        <div
            class="theme-selector-micro-star absolute top-[.89rem] left-[.85rem]"
        >
            <div class="transition duration-300">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="size-[5px]"
                    viewBox="0 0 256 256"
                    aria-hidden="true"
                    focusable="false"
                >
                    <path
                        fill="currentColor"
                        d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0L89.93 166L26.5 143a16 16 0 0 1 0-30L90 89.93l23-63.43a16 16 0 0 1 30 0L166.07 90l63.43 23a15.79 15.79 0 0 1 10.5 15Z"
                    />
                </svg>
            </div>
        </div>
        {{-- Sun ball (shows when effective theme is light) --}}
        <div
            class="absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2"
        >
            <div class="theme-selector-sunball">
                <div
                    class="size-3 rounded-full bg-current transition duration-300"
                ></div>
            </div>
        </div>
        {{-- sunShine --}}
        <div
            class="theme-selector-sunshine absolute inset-0 grid h-full w-full"
        >
            <div class="relative h-full w-full">
                <div
                    class="absolute top-[0.45rem] right-1/2 h-[3px] w-[1.5px] translate-x-1/2 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute top-1/2 right-[0.45rem] h-[3px] w-[1.5px] -translate-y-1/2 rotate-90 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute right-1/2 bottom-[0.45rem] h-[3px] w-[1.5px] translate-x-1/2 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute top-1/2 left-[0.45rem] h-[3px] w-[1.5px] -translate-y-1/2 rotate-90 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute top-[0.65rem] right-[0.65rem] h-[3px] w-[1.5px] rotate-45 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute top-[0.65rem] left-[0.65rem] h-[3px] w-[1.5px] -rotate-45 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute right-[0.65rem] bottom-[0.65rem] h-[3px] w-[1.5px] -rotate-45 rounded-full bg-current transition duration-300"
                ></div>
                <div
                    class="absolute bottom-[0.65rem] left-[0.65rem] h-[3px] w-[1.5px] rotate-45 rounded-full bg-current transition duration-300"
                ></div>
            </div>
        </div>
    </div>

    {{-- Hover hint: Current -> Next preference --}}
    <div
        class="pointer-events-none absolute top-full left-1/2 -translate-x-1/2 translate-y-2 text-[10px] leading-none whitespace-nowrap text-gray-500 opacity-0 transition duration-200 group-hover:translate-y-1 group-hover:opacity-100 dark:text-gray-400"
        aria-hidden="true"
        x-text="hintText()"
    ></div>
</div>
