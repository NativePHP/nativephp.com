<section
    class="mt-10 px-5 md:mt-14"
    aria-labelledby="hero-title"
>
    {{-- Header --}}
    <header
        class="group/header relative isolate grid place-items-center gap-0.5 text-center dark:text-white/90"
    >
        {{-- Build --}}
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
            class="truncate text-6xl font-extrabold uppercase min-[400px]:text-7xl md:text-8xl"
        >
            Build
            <span class="sr-only">Native PHP Apps</span>
        </h1>
        {{-- Native --}}
        <div class="relative">
            <h1
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                x: [10, 0],
                            },
                            {
                                duration: 1,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="truncate text-6xl font-extrabold text-violet-400 uppercase min-[400px]:text-7xl md:text-8xl"
                aria-hidden="true"
            >
                Native
            </h1>

            {{-- Blurred circle --}}
            <div
                class="absolute -top-20 size-48 rounded-full bg-white/60 blur-[100px] md:-right-32 md:size-60 dark:-top-80 dark:right-1/2 dark:-z-50 dark:size-80 dark:translate-x-1/2 dark:bg-[#444892]/80"
                aria-hidden="true"
            ></div>

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
                class="absolute -top-10 -right-10"
            >
                <x-icons.star
                    x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        rotate: [0, -180],
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
                    class="size-8 text-[#E4DFFB] dark:size-6"
                />
            </div>

            {{-- Glass shape --}}
            <div
                class="absolute top-16 -left-12 size-6 rounded-tl-3xl rounded-tr-xl rounded-br-3xl rounded-bl-xl bg-[#5A31FF]/10 ring-1 ring-white/50 backdrop-blur-xs min-[400px]:top-18 min-[400px]:-left-14 min-[400px]:size-8 md:top-[5.6rem] md:-left-18 md:size-10 dark:hidden dark:ring-gray-700/50"
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                rotate: [-90, 0],
                                scale: [0, 1],
                                opacity: [0, 1],
                            },
                            {
                                duration: 1.5,
                                ease: motion.anticipate,
                            },
                        )
                    }
                "
                aria-hidden="true"
            ></div>

            {{-- Video introduction --}}
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
                class="group absolute -top-[5.7rem] -right-76 hidden items-end gap-1 text-left text-sm lg:flex"
            >
                <div class="relative -top-1.5 -mr-6 flex items-end gap-1">
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
                        href="https://www.youtube.com/watch?v=CsM66a0koAM"
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
                        <span class="sr-only">Play introduction video</span>
                    </a>
                </div>
                <div>
                    <div class="font-medium">Video</div>
                    <div class="font-normal text-gray-600 dark:text-white/50">
                        NativePHP for Mobile
                    </div>
                    {{-- Image --}}
                    <a
                        href="https://www.youtube.com/watch?v=CsM66a0koAM"
                        target="_blank"
                        rel="noopener"
                        aria-label="Watch Simon Hamp's Laracon EU talk about building mobile apps with PHP"
                    >
                        <img
                            src="{{ Vite::asset('resources/images/simon2025laraconeu.webp') }}"
                            alt="Simon Hamp presenting at Laracon EU 2025 on building mobile apps with PHP"
                            class="mt-2 w-40 rounded-xl"
                            width="160"
                            height="90"
                            loading="lazy"
                        />
                    </a>
                </div>
            </div>
        </div>
        {{-- PHP Apps --}}
        <h1
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
            class="truncate text-6xl font-extrabold uppercase min-[400px]:text-7xl md:text-8xl"
            aria-hidden="true"
        >
            PHP Apps
        </h1>

        {{-- Shiny line --}}
        <div
            class="absolute top-32 left-1/2 z-20 -translate-x-1/2 rotate-50 transition duration-500 ease-out will-change-transform group-hover/header:translate-x-[-55%] group-hover/header:opacity-0"
            aria-hidden="true"
        >
            <div
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                y: [-30, 0],
                                opacity: [0, 0, 1],
                            },
                            {
                                duration: 1.2,
                                ease: motion.easeOut,
                            },
                        )
                    }
                "
                class="h-2.5 w-104 bg-linear-to-r from-transparent to-white/50 ring-1 ring-white/50 dark:hidden"
            ></div>
        </div>
    </header>

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
        class="mx-auto max-w-4xl pt-5 text-center text-lg/relaxed text-gray-600 md:text-xl/relaxed dark:text-zinc-400"
        aria-describedby="hero-title"
    >
        Bring your
        <a
            href="https://www.php.net"
            target="_blank"
            rel="noopener"
            class="inline-block font-medium text-[#7a8bd7] transition duration-200 will-change-transform hover:-translate-y-0.5 dark:text-[#92a6ff]"
            aria-label="Learn more about PHP programming language"
        >
            PHP
        </a>
        &
        <a
            href="https://laravel.com"
            target="_blank"
            rel="noopener"
            class="inline-block font-medium text-[#F53003] transition duration-200 will-change-transform hover:-translate-y-0.5"
            aria-label="Learn more about Laravel framework"
        >
            Laravel
        </a>
        skills to the world of
        <span class="text-black dark:text-white">desktop & mobile apps</span>
        .
        <br class="hidden md:block" />
        Build cross-platform applications effortlessly—no extra tools, just the
        stack you love.
    </p>

    {{-- Call to Action Button --}}
    <div
        class="mt-10 flex flex-wrap-reverse items-center justify-center gap-4 sm:flex-nowrap"
        x-data="{ desktopHover: false, mobileHover: false }"
    >
        {{-- Desktop --}}
        <div
            x-init="
                () => {
                    motion.animate(
                        $el,
                        {
                            opacity: [0, 1],
                            x: [10, 0],
                        },
                        {
                            duration: 1,
                            ease: motion.easeOut,
                        },
                    )
                }
            "
            class="w-full max-w-64"
        >
            <div
                class="transition duration-300"
                :class="{ 'opacity-60 grayscale': mobileHover }"
            >
                <a
                    href="/docs/desktop/1"
                    class="group dark:bg-haiti relative isolate z-0 flex h-16 items-center justify-between gap-3 overflow-hidden rounded-2xl bg-[#EBEDF2] pr-6 pl-5 leading-snug transition duration-200 ease-in-out will-change-transform hover:bg-[#e5d6ff] dark:hover:bg-indigo-900/60"
                    aria-label="Get started with NativePHP documentation for desktop apps"
                    x-on:mouseenter="desktopHover = true"
                    x-on:mouseleave="desktopHover = false"
                >
                    {{-- Arrow --}}
                    <div class="flex items-center gap-1">
                        <div
                            class="size-1 rounded-full bg-current transition duration-500 ease-in-out will-change-transform group-hover:translate-x-2 group-hover:translate-y-1.5 group-hover:opacity-50"
                        ></div>
                        <div class="flex flex-col gap-2">
                            <div
                                class="size-1 rounded-full bg-current opacity-50 transition duration-500 ease-in-out will-change-transform group-hover:-translate-x-2 group-hover:translate-y-1.5 group-hover:opacity-100"
                            ></div>
                            <div
                                class="size-1 rounded-full bg-current opacity-50 transition duration-500 ease-in-out will-change-transform group-hover:-translate-y-3"
                            ></div>
                        </div>
                    </div>
                    {{-- Label --}}
                    <div
                        class="flex items-center gap-3 duration-500 ease-in-out will-change-transform group-hover:-translate-x-1"
                    >
                        <div>Desktop</div>
                        <x-icons.pc class="size-7 shrink-0" />
                    </div>
                </a>
            </div>
        </div>

        {{-- Mobile --}}
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
            class="w-full max-w-64"
        >
            <div
                class="transition duration-300"
                :class="{ 'opacity-60 grayscale': desktopHover }"
            >
                <a
                    href="/mobile"
                    class="group dark:bg-haiti relative isolate z-0 flex h-16 items-center justify-between gap-3 overflow-hidden rounded-2xl bg-[#EBEDF2] pr-5 pl-6 leading-snug transition duration-200 ease-in-out will-change-transform hover:bg-[#e5d6ff] dark:hover:bg-indigo-900/50"
                    aria-label="Get started with NativePHP documentation for mobile apps"
                    x-on:mouseenter="mobileHover = true"
                    x-on:mouseleave="mobileHover = false"
                >
                    {{-- Label --}}
                    <div
                        class="flex items-center gap-3 duration-500 ease-in-out will-change-transform group-hover:translate-x-1"
                    >
                        <x-icons.device-mobile-phone class="size-6 shrink-0" />
                        <div>Mobile</div>
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
                        class="absolute -bottom-12 left-14 -z-10 h-20 w-44 rounded-full bg-[#D3D3FF] blur-xl will-change-transform dark:bg-blue-500/30"
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
                        class="absolute -bottom-12 -left-5 -z-20 h-20 w-44 rounded-full bg-[#FFE7D3] blur-xl will-change-transform dark:bg-indigo-500/30"
                    ></div>
                </a>
            </div>
        </div>
    </div>
</section>
