<div
    class="theme-selector relative -mx-1.5 h-10 w-10 cursor-pointer select-none hover:text-slate-600 dark:opacity-70 dark:hover:text-[#bcc1ef]"
    x-on:click="darkMode = !darkMode"
    x-data="{
        nightToDay: [
            [
                '.theme-selector-moon',
                {
                    opacity: [1, 0],
                    transform:
                        'translate(0%, -50%) translate(12px, 0px) rotate(70deg) scale(0.6, 0.6)',
                },
                { duration: 0.3, ease: motion.easeOut },
            ],

            [
                '.theme-selector-mini-star',
                { opacity: [1, 0], scale: [1, 0] },
                { duration: 0.3, ease: motion.easeOut, at: 0 },
            ],

            [
                '.theme-selector-micro-star',
                { opacity: [1, 0], scale: [1, 0] },
                { duration: 0.3, ease: motion.easeOut, at: 0 },
            ],

            [
                '.theme-selector-sunball',
                { opacity: [0, 1], x: [-5, 0], y: [-5, 0], scale: [0, 1] },
                {
                    duration: 0.3,
                    ease: motion.easeOut,
                    at: 0.15,
                },
            ],

            [
                '.theme-selector-sunshine',
                { opacity: [0, 1], scale: [0, 1], rotate: [-180, 0] },
                {
                    duration: 0.3,
                    ease: motion.easeOut,
                    at: 0.15,
                },
            ],
        ],
        dayToNight: [
            [
                '.theme-selector-sunshine',
                { opacity: [1, 0], scale: [1, 0], rotate: [0, -180] },
                {
                    duration: 0.3,
                    ease: motion.easeOut,
                },
            ],

            [
                '.theme-selector-sunball',
                { opacity: [1, 0], x: [0, -5], y: [0, -5], scale: [1, 0] },
                {
                    duration: 0.3,
                    ease: motion.easeOut,
                    at: 0.15,
                },
            ],

            [
                '.theme-selector-micro-star',
                { opacity: [0, 1], scale: [0, 1] },
                { duration: 0.3, ease: motion.easeOut, at: 0 },
            ],

            [
                '.theme-selector-mini-star',
                { opacity: [0, 1], scale: [0, 1] },
                { duration: 0.3, ease: motion.easeOut, at: 0 },
            ],

            [
                '.theme-selector-moon',
                {
                    opacity: [0, 1],
                    transform: 'translate(0%, -50%) translate(12px, 0px)',
                },
                { duration: 0.3, ease: motion.easeOut, at: 0 },
            ],
        ],
        setInitialState() {
            // Set initial state directly without animation
            if (! darkMode) {
                document.querySelector('.theme-selector-sunball').style.opacity =
                    '0'
                document.querySelector('.theme-selector-sunshine').style.opacity =
                    '0'
                document.querySelector('.theme-selector-moon').style.transform =
                    'translate(0%, -50%) translate(12px, 0px)'
            } else {
                document.querySelector('.theme-selector-moon').style.opacity = '0'
                document.querySelector('.theme-selector-moon').style.transform =
                    'translate(0%, -50%) translate(12px, 0px) rotate(70deg) scale(0.6, 0.6)'
                document.querySelector('.theme-selector-mini-star').style.opacity =
                    '0'
                document.querySelector('.theme-selector-micro-star').style.opacity =
                    '0'
            }
        },
        init() {
            motion.animate($el, { scale: 1 }, { duration: 0.1 })

            this.setInitialState()

            // Watch for dark mode changes
            $watch('darkMode', (value) => {
                if (value) {
                    motion.animate(this.nightToDay)
                } else {
                    motion.animate(this.dayToNight)
                }
            })
        },
    }"
>
    {{-- Moon --}}
    <div
        class="theme-selector-moon absolute right-1/2 top-1/2 -translate-y-1/2 translate-x-1/2"
    >
        <div class="-scale-x-100 transition duration-300">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-[22px]"
                viewBox="0 0 16 16"
            >
                <path
                    fill="currentColor"
                    d="M6 .278a.768.768 0 0 1 .08.858a7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277c.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316a.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71C0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z"
                />
            </svg>
        </div>
    </div>
    {{-- Mini star --}}
    <div class="theme-selector-mini-star absolute left-[.6rem] top-[.6rem]">
        <div class="transition duration-300">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-[7px]"
                viewBox="0 0 256 256"
            >
                <path
                    fill="currentColor"
                    d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0L89.93 166L26.5 143a16 16 0 0 1 0-30L90 89.93l23-63.43a16 16 0 0 1 30 0L166.07 90l63.43 23a15.79 15.79 0 0 1 10.5 15Z"
                />
            </svg>
        </div>
    </div>
    {{-- Micro star --}}
    <div class="theme-selector-micro-star absolute left-[1rem] top-[1rem]">
        <div class="transition duration-300">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-[5px]"
                viewBox="0 0 256 256"
            >
                <path
                    fill="currentColor"
                    d="M240 128a15.79 15.79 0 0 1-10.5 15l-63.44 23.07L143 229.5a16 16 0 0 1-30 0L89.93 166L26.5 143a16 16 0 0 1 0-30L90 89.93l23-63.43a16 16 0 0 1 30 0L166.07 90l63.43 23a15.79 15.79 0 0 1 10.5 15Z"
                />
            </svg>
        </div>
    </div>
    {{-- Sun ball --}}
    <div class="absolute right-1/2 top-1/2 -translate-y-1/2 translate-x-1/2">
        <div class="theme-selector-sunball">
            <div
                class="size-3.5 rounded-full bg-current transition duration-300"
            ></div>
        </div>
    </div>
    {{-- sunShine --}}
    <div class="theme-selector-sunshine absolute inset-0 grid h-full w-full">
        <div class="relative h-full w-full">
            {{-- Top --}}
            <div
                class="absolute right-1/2 top-[0.45rem] h-[3.5px] w-[2px] translate-x-1/2 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Right --}}
            <div
                class="absolute right-[0.45rem] top-1/2 h-[3.5px] w-[2px] -translate-y-1/2 rotate-[90deg] rounded-full bg-current transition duration-300"
            ></div>
            {{-- Bottom --}}
            <div
                class="absolute bottom-[0.45rem] right-1/2 h-[3.5px] w-[2px] translate-x-1/2 rounded-full bg-current transition duration-300"
            ></div>
            {{-- Left --}}
            <div
                class="absolute left-[0.45rem] top-1/2 h-[3.5px] w-[2px] -translate-y-1/2 rotate-[90deg] rounded-full bg-current transition duration-300"
            ></div>
            {{-- Top Right --}}
            <div
                class="absolute right-[0.7rem] top-[0.7rem] h-[3.5px] w-[2px] rotate-[45deg] rounded-full bg-current transition duration-300"
            ></div>
            {{-- Top Left --}}
            <div
                class="absolute left-[0.7rem] top-[0.7rem] h-[3.5px] w-[2px] rotate-[-45deg] rounded-full bg-current transition duration-300"
            ></div>

            {{-- Bottom Right --}}
            <div
                class="absolute bottom-[0.7rem] right-[0.7rem] h-[3.5px] w-[2px] rotate-[-45deg] rounded-full bg-current transition duration-300"
            ></div>
            {{-- Bottom Left --}}
            <div
                class="absolute bottom-[0.7rem] left-[0.7rem] h-[3.5px] w-[2px] rotate-[45deg] rounded-full bg-current transition duration-300"
            ></div>
        </div>
    </div>
</div>
