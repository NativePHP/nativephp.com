<div
    x-on:click="darkMode = !darkMode"
    x-data="{
        timeline: null,

        init() {
            gsap.to('.theme-selector', {
                scale: 1,
                duration: 0.1,
            })

            timeline = gsap
                .timeline({
                    paused: true,
                })
                .to('.theme-selector-moon', {
                    rotate: 70,
                    ease: 'sine.out',
                    duration: 0.3,
                })
                .to(
                    '.theme-selector-mini-star',
                    {
                        autoAlpha: 0,
                        scale: 0,
                        ease: 'sine.out',
                        duration: 0.3,
                    },
                    '>-0.3',
                )
                .to(
                    '.theme-selector-micro-star',
                    {
                        autoAlpha: 0,
                        scale: 0,
                        ease: 'sine.out',
                        duration: 0.3,
                    },
                    '<',
                )
                .to(
                    '.theme-selector-moon',
                    {
                        scale: 0.6,
                        ease: 'sine.out',
                        duration: 0.3,
                    },
                    '<',
                )
                .fromTo(
                    '.theme-selector-sunball',
                    {
                        scale: 0,
                        x: -5,
                        y: 5,
                    },
                    {
                        x: 0,
                        y: 0,
                        scale: 1,
                        ease: 'expo.out',
                        duration: 0.3,
                    },
                    '>-0.15',
                )
                .fromTo(
                    '.theme-selector-sunshine',
                    {
                        scale: 0,
                        rotate: -180,
                    },
                    {
                        scale: 1,
                        rotate: 0,
                        ease: 'expo.out',
                        duration: 0.3,
                    },
                    '<',
                )

            if (darkMode) timeline.progress(1)

            $watch('darkMode', (value) => {
                if (value) {
                    timeline.play()
                } else {
                    timeline.reverse()
                }
            })
        },
    }"
    role="button"
    aria-label="Toggle between light and dark mode"
    x-bind:aria-pressed="darkMode ? 'true' : 'false'"
    tabindex="0"
    class="theme-selector relative -mx-1.5 size-9 cursor-pointer select-none hover:text-slate-600 dark:opacity-70 dark:hover:text-[#bcc1ef]"
>
    <span class="sr-only">Toggle theme</span>

    {{-- Moon --}}
    <div
        class="theme-selector-moon absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2"
        aria-hidden="true"
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
    <div
        class="theme-selector-mini-star absolute top-[.5rem] left-[.5rem]"
        aria-hidden="true"
    >
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
        class="theme-selector-micro-star absolute top-[.9rem] left-[.9rem]"
        aria-hidden="true"
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
    {{-- Sun ball --}}
    <div
        class="absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2"
        aria-hidden="true"
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
        aria-hidden="true"
    >
        <div class="relative h-full w-full">
            {{-- Top --}}
            <div
                class="absolute top-[0.45rem] right-1/2 h-[3px] w-[1.5px] translate-x-1/2 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Right --}}
            <div
                class="absolute top-1/2 right-[0.45rem] h-[3px] w-[1.5px] -translate-y-1/2 rotate-90 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Bottom --}}
            <div
                class="absolute right-1/2 bottom-[0.45rem] h-[3px] w-[1.5px] translate-x-1/2 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Left --}}
            <div
                class="absolute top-1/2 left-[0.45rem] h-[3px] w-[1.5px] -translate-y-1/2 rotate-90 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Top Right --}}
            <div
                class="absolute top-[0.65rem] right-[0.65rem] h-[3px] w-[1.5px] rotate-45 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Top Left --}}
            <div
                class="absolute top-[0.65rem] left-[0.65rem] h-[3px] w-[1.5px] -rotate-45 rounded-full bg-current transition duration-300"
            ></div>

            {{-- Bottom Right --}}
            <div
                class="absolute right-[0.65rem] bottom-[0.65rem] h-[3px] w-[1.5px] -rotate-45 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Bottom Left --}}
            <div
                class="absolute bottom-[0.65rem] left-[0.65rem] h-[3px] w-[1.5px] rotate-45 rounded-full bg-current transition duration-300"
            ></div>
        </div>
    </div>
</div>
