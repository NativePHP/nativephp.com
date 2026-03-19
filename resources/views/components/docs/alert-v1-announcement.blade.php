<div
    class="relative z-0 mt-5 flex flex-col items-start gap-3 overflow-hidden rounded-2xl bg-[#f9f8f6] p-4 ring-1 ring-black/5 min-[450px]:flex-row min-[450px]:items-center dark:bg-mirage"
    role="alert"
    aria-labelledby="beta-alert-title"
    aria-describedby="beta-alert-description"
>
    {{-- Blue circle --}}
    <div
        class="absolute left-6 top-0 -z-10 hidden h-32 w-6 -rotate-60 rounded-full blur-lg min-[450px]:top-1/2 min-[450px]:-translate-y-1/2 dark:block dark:bg-linear-to-b dark:from-blue-500 dark:to-blue-500/10"
        aria-hidden="true"
    ></div>

    {{-- White circle --}}
    <div
        class="absolute left-20 top-0 -z-20 hidden h-32 w-3 -rotate-60 rounded-full blur-lg min-[450px]:top-1/2 min-[450px]:-translate-y-1/2 dark:block dark:bg-linear-to-b dark:from-white/30 dark:to-transparent"
        aria-hidden="true"
    ></div>

    {{-- Icon --}}
    <div
        class="grid size-10 place-items-center rounded-full bg-gray-200/50 backdrop-blur-xs dark:bg-black/30"
        aria-hidden="true"
    >
        <x-icons.colored-confetti
            class="-mr-px size-[22px] shrink-0 mix-blend-multiply dark:hidden"
            aria-hidden="true"
        />

        <x-icons.confetti
            class="hidden size-5 shrink-0 dark:block"
            aria-hidden="true"
        />
    </div>

    {{-- Title --}}
    <h2 class="font-medium">
        NativePHP for Desktop and Mobile have reached v1!
    </h2>

    {{-- Dot 1 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random() * 1.5,
                    },
                )
            }
        "
        class="absolute -left-3 top-2 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 11 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 - 5,
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random(),
                    },
                )
            }
        "
        class="absolute -left-3 top-3.5 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 2 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random() * 1.5,
                    },
                )
            }
        "
        class="absolute -left-3 top-5 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 22 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 - 5,
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random(),
                    },
                )
            }
        "
        class="absolute -left-3 top-7 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 3 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random() * 1.5,
                    },
                )
            }
        "
        class="absolute -left-3 top-10 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 33 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 - 5,
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random(),
                    },
                )
            }
        "
        class="absolute -left-3 top-12 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 4 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random() * 1.5,
                    },
                )
            }
        "
        class="absolute -left-3 top-14 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>

    {{-- Dot 44 --}}
    <div
        x-init="
            () => {
                motion.animate(
                    $el,
                    {
                        opacity: [0, 1, 0],
                        y: [
                            Math.random() * 10 - 5,
                            Math.random() * 10 + 5,
                            Math.random() * 10 - 5,
                        ],
                        x: [Math.random() * 10 - 5, 120],
                    },
                    {
                        duration: Math.random() * 5 + 3,
                        repeat: Infinity,
                        repeatType: 'loop',
                        ease: motion.easeInOut,
                        delay: Math.random(),
                    },
                )
            }
        "
        class="absolute -left-3 top-16 -z-50 hidden size-0.5 rounded-full bg-white dark:block"
        aria-hidden="true"
    ></div>
</div>
