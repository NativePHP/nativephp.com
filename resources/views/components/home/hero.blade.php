<section
    class="mt-2"
    aria-labelledby="hero-title"
>
    <div
        class="relative z-0 overflow-hidden rounded-2xl bg-gradient-to-t from-[#E0E5EB] to-[#F9F9F9] p-10 dark:from-slate-950 dark:to-slate-900 dark:ring-1 dark:ring-slate-800"
    >
        {{-- Mockups --}}
        <div class="flex items-end justify-center">
            <img
                src="{{ Vite::asset('resources/images/home/macbook.webp') }}"
                alt=""
                class="w-90"
            />
            <img
                src="{{ Vite::asset('resources/images/home/iphone.webp') }}"
                alt=""
                class="-ml-18 h-45"
            />
        </div>

        {{-- Main --}}
        <div class="mt-8 grid place-items-center text-center text-pretty">
            {{-- Video --}}
            <div class="-mt-40 -mr-7 flex w-full justify-end">
                <div
                    x-init="
                        () => {
                            motion.animate(
                                $el,
                                {
                                    y: [-10, 0],
                                    x: [10, 0],
                                },
                                {
                                    duration: 1.5,
                                    ease: motion.circOut,
                                },
                            )
                        }
                    "
                    class="group hidden items-end gap-1 text-left lg:flex"
                >
                    <div class="relative top-0.5 -mr-6 flex items-end gap-1">
                        {{-- Black circle --}}
                        <div
                            x-init="
                                () => {
                                    motion.animate(
                                        $el,
                                        {
                                            scale: [0, 1],
                                        },
                                        {
                                            duration: 1,
                                            ease: motion.backOut,
                                        },
                                    )
                                }
                            "
                            class="-mb-1.5 size-1 rounded-full bg-white ring-[3px] ring-black dark:bg-black/50 dark:ring-white"
                            aria-hidden="true"
                        ></div>
                        {{-- Line --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="94"
                            height="41"
                            viewBox="0 0 94 41"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                x-init="
                                    () => {
                                        motion.animate(
                                            $el,
                                            {
                                                strokeDashoffset: [0, 20],
                                            },
                                            {
                                                duration: 1.5,
                                                repeat: Infinity,
                                                repeatType: 'loop',
                                                ease: 'linear',
                                            },
                                        )
                                    }
                                "
                                d="M94 0.5H47.3449C41.942 0.5 36.7691 2.68588 33.0033 6.56012L0.5 40"
                                stroke="currentColor"
                                stroke-dasharray="5 5"
                            />
                        </svg>
                        {{-- Play button --}}
                        <a
                            href="https://www.youtube.com/watch?v=WOTSjPFXQ2k"
                            target="_blank"
                            rel="noopener"
                            class="relative -top-5 grid size-10 place-items-center rounded-full bg-black/30 text-white ring-1 ring-white/10 backdrop-blur-sm transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d] dark:group-hover:text-[#9c90f0]"
                            aria-label="Watch NativePHP introduction video on YouTube"
                        >
                            <x-icons.play-button
                                x-init="
                                        () => {
                                            motion.animate(
                                                $el,
                                                {
                                                    x: [-1, 1],
                                                },
                                                {
                                                    duration: 0.6,
                                                    repeat: Infinity,
                                                    repeatType: 'mirror',
                                                    ease: motion.easeInOut,
                                                },
                                            )
                                        }
                                    "
                                class="size-4"
                                aria-hidden="true"
                            />
                            <span class="sr-only">Play introduction</span>
                        </a>
                    </div>
                    <div>
                        <div class="text-xs text-gray-600 dark:text-white/50">
                            Introducing
                        </div>
                        <div class="text-sm font-medium">
                            NativePHP for Mobile
                        </div>
                        {{-- Image --}}
                        <a
                            href="https://www.youtube.com/watch?v=WOTSjPFXQ2k"
                            target="_blank"
                            rel="noopener"
                            aria-label="Watch Simon Hamp's Laracon EU talk about building mobile apps with PHP"
                        >
                            <img
                                src="{{ Vite::asset('resources/images/home/video_introduction_thumbnail.webp') }}"
                                alt="Simon Hamp presenting at Laracon EU 2025 on building mobile apps with PHP"
                                class="mt-2 w-35 rounded-2xl"
                                width="140"
                                height="80"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            </div>

            {{-- Headline --}}
            <h1
                id="hero-title"
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [-10, 0],
                            },
                            {
                                duration: 1,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="relative bg-gradient-to-r from-gray-500 to-gray-800 bg-clip-text text-3xl font-bold text-transparent dark:text-white"
            >
                The only way to build native apps in PHP

                {{-- Star --}}
                <div
                    x-init="
                        () => {
                            motion.animate(
                                $el,
                                {
                                    scale: [0, 1],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 1,
                                    ease: motion.anticipate,
                                },
                            )
                        }
                    "
                    class="absolute -top-4 -left-4"
                >
                    <x-icons.star
                        x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        rotate: [0, 180],
                                    },
                                    {
                                        duration: 3,
                                        repeat: Infinity,
                                        repeatType: 'loop',
                                        ease: 'linear',
                                    },
                                )
                            }
                        "
                        class="size-4 text-gray-500 dark:size-6"
                    />
                </div>
            </h1>

            {{-- Description --}}
            <p
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [10, 0],
                            },
                            {
                                duration: 1,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="mx-auto mt-2.5 max-w-4xl text-center text-lg/relaxed text-gray-600 dark:text-zinc-400"
                aria-describedby="hero-title"
            >
                Bring your
                <a
                    href="https://www.php.net"
                    target="_blank"
                    rel="noopener"
                    class="inline-block font-medium text-gray-900 transition duration-200 will-change-transform hover:-translate-y-0.5 dark:text-white"
                    aria-label="Learn more about PHP programming language"
                >
                    PHP
                </a>
                &
                <a
                    href="https://laravel.com"
                    target="_blank"
                    rel="noopener"
                    class="inline-block font-medium text-gray-900 transition duration-200 will-change-transform hover:-translate-y-0.5 dark:text-white"
                    aria-label="Learn more about Laravel framework"
                >
                    Laravel
                </a>
                skills to the world of
                <span class="text-gray-900 dark:text-white">
                    desktop & mobile apps
                </span>
                .
                <br class="hidden md:block" />
                Build cross-platform applications effortlessly—no extra tools,
                just the stack you love.
            </p>

            {{-- Call to action --}}
            <div
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [-10, 0],
                            },
                            {
                                duration: 1,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="mt-3.5 w-full max-w-55"
            >
                <div class="transition duration-300">
                    <a
                        href="/docs/mobile/1/getting-started/introduction"
                        class="group dark:bg-haiti relative isolate z-0 flex h-15 items-center justify-between gap-3 overflow-hidden rounded-3xl bg-gray-900 px-5 leading-snug text-white transition duration-200 ease-in-out will-change-transform hover:bg-gray-800 dark:hover:bg-indigo-900/50"
                        aria-label="Get started with NativePHP documentation for mobile apps"
                    >
                        {{-- Label --}}
                        <div
                            class="bg-gradient-to-br from-white to-cyan-300 bg-clip-text text-transparent duration-500 ease-in-out will-change-transform group-hover:translate-x-1"
                        >
                            Start building
                        </div>
                        {{-- Arrow --}}
                        <div class="flex items-center gap-1">
                            <div class="flex flex-col gap-2">
                                <div
                                    class="size-1 rounded-full bg-current opacity-50 transition duration-500 ease-in-out will-change-transform group-hover:translate-x-2 group-hover:translate-y-1.5 group-hover:opacity-100"
                                ></div>
                                <div
                                    class="size-1 rounded-full bg-current opacity-50 transition duration-500 ease-in-out will-change-transform group-hover:-translate-y-3"
                                ></div>
                            </div>
                            <div
                                class="size-1 rounded-full bg-current transition duration-500 ease-in-out will-change-transform group-hover:-translate-x-2 group-hover:translate-y-1.5 group-hover:opacity-50"
                            ></div>
                        </div>
                        {{-- Blue blur --}}
                        <div
                            x-init="
                                () => {
                                    motion.animate(
                                        $el,
                                        {
                                            x: [0, 20, -100, 0],
                                            y: [0, 5, 0],
                                            scale: [1, 0.7, 1],
                                            rotate: [0, 10, 0],
                                        },
                                        {
                                            duration: 10,
                                            repeat: Infinity,
                                            repeatType: 'loop',
                                            ease: motion.easeInOut,
                                        },
                                    )
                                }
                            "
                            class="absolute -bottom-12 left-14 -z-10 h-20 w-44 rounded-full bg-transparent blur-xl will-change-transform dark:bg-blue-500/30"
                        ></div>
                        {{-- Orange blur --}}
                        <div
                            x-init="
                                () => {
                                    motion.animate(
                                        $el,
                                        {
                                            x: [0, -10, 0],
                                            y: [0, 10, 0],
                                            scale: [1, 1.2, 1],
                                        },
                                        {
                                            duration: 5,
                                            repeat: Infinity,
                                            repeatType: 'loop',
                                            ease: motion.easeInOut,
                                        },
                                    )
                                }
                            "
                            class="absolute -bottom-12 -left-5 -z-20 h-20 w-44 rounded-full bg-transparent blur-xl will-change-transform dark:bg-cyan-500/30"
                        ></div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Top right vertical lines --}}
        <div class="absolute top-0 right-0 -z-18 mask-l-from-30%">
            <div class="-scale-x-100 -scale-y-100">
                <x-home.vertical-lines />
            </div>
        </div>

        {{-- Bottom left vertical lines --}}
        <div class="absolute bottom-0 left-0 -z-18 mask-r-from-30%">
            <x-home.vertical-lines />
        </div>

        {{-- Green blur --}}
        <div
            class="absolute -right-20 bottom-1/2 -z-19 size-70 rounded-full bg-emerald-100 blur-[100px] dark:bg-emerald-500/20"
        ></div>

        {{-- Cyan blur --}}
        <div
            class="absolute top-1/2 -left-20 -z-20 size-100 rounded-full bg-cyan-100 blur-[100px] dark:bg-cyan-500/20"
        ></div>
    </div>

    {{-- Simon talk for mobile --}}
    <div class="grid place-items-center pt-4 lg:hidden">
        <a
            href="https://www.youtube.com/watch?v=CsM66a0koAM"
            target="_blank"
            rel="noopener"
            class="group relative"
            aria-label="Watch Simon Hamp's talk on building mobile apps with PHP"
        >
            {{-- Play button --}}
            <div
                class="absolute top-1/2 right-1/2 grid size-16 translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur-sm transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
                aria-hidden="true"
            >
                <x-icons.play-button
                    x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        x: [0, 3],
                                    },
                                    {
                                        duration: 0.6,
                                        repeat: Infinity,
                                        repeatType: 'mirror',
                                        ease: motion.easeInOut,
                                    },
                                )
                            }
                        "
                    class="size-7"
                    aria-hidden="true"
                />
                <span class="sr-only">Play video</span>
            </div>
            {{-- Image --}}
            <img
                src="{{ Vite::asset('resources/images/simon2025laraconeu.webp') }}"
                alt="Simon Hamp presenting at Laracon EU 2025 on building mobile apps with PHP"
                class="w-full max-w-[505px] rounded-xl"
                width="505"
                height="284"
                loading="lazy"
            />
        </a>
    </div>
</section>
