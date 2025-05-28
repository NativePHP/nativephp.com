<x-layout title="Baking Delicious Native Apps">
    {{-- Hero --}}
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
                    class="truncate text-6xl font-extrabold uppercase text-violet-400 min-[400px]:text-7xl md:text-8xl"
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
                    class="absolute -right-10 -top-10"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 40 40"
                        fill="none"
                        class="size-8 dark:size-6"
                        aria-hidden="true"
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
                    >
                        <path
                            d="M25.66 17.636L40 20L25.66 22.364C23.968 22.644 22.64 23.968 22.364 25.66L20 40L17.636 25.66C17.356 23.968 16.032 22.64 14.34 22.364L0 20L14.34 17.636C16.032 17.356 17.36 16.032 17.636 14.34L20 0L22.364 14.34C22.644 16.032 23.968 17.36 25.66 17.636Z"
                            fill="#E4DFFB"
                        />
                    </svg>
                </div>

                {{-- Glass shape --}}
                <div
                    class="absolute -left-[3rem] top-[4rem] size-6 rounded-bl-xl rounded-br-3xl rounded-tl-3xl rounded-tr-xl bg-[#5A31FF]/10 ring-1 ring-white/50 backdrop-blur-sm min-[400px]:-left-[3.5rem] min-[400px]:top-[4.5rem] min-[400px]:size-8 md:-left-[4.5rem] md:top-[5.6rem] md:size-10 dark:hidden dark:ring-gray-700/50"
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
                    class="group absolute -right-[19rem] -top-[5.7rem] hidden items-end gap-1 text-left text-sm lg:flex"
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
                            class="relative -top-5 grid size-10 place-items-center rounded-full bg-black/30 text-white ring-1 ring-white/10 backdrop-blur transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d] dark:group-hover:text-[#9c90f0]"
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
                        <div
                            class="font-normal text-gray-600 dark:text-white/50"
                        >
                            NativePHP for mobile
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
                class="absolute left-1/2 top-32 z-20 -translate-x-1/2 rotate-[50deg] transition duration-500 ease-out will-change-transform group-hover/header:translate-x-[-55%] group-hover/header:opacity-0"
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
                    class="h-2.5 w-[26rem] bg-gradient-to-r from-transparent to-white/50 ring-1 ring-white/50 dark:hidden"
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
                    class="absolute right-1/2 top-1/2 grid size-16 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
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
            <span class="text-black dark:text-white">
                desktop & mobile apps
            </span>
            .
            <br class="hidden md:block" />
            Build cross-platform applications effortlessly—no extra tools, just
            the stack you love.
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
                        class="group relative isolate z-0 flex h-16 items-center justify-between gap-3 overflow-hidden rounded-2xl bg-[#EBEDF2] pl-5 pr-6 leading-snug transition duration-200 ease-in-out will-change-transform hover:bg-[#e5d6ff] dark:bg-haiti dark:hover:bg-indigo-900/60"
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
                        href="/docs/mobile/1"
                        class="group relative isolate z-0 flex h-16 items-center justify-between gap-3 overflow-hidden rounded-2xl bg-[#EBEDF2] pl-6 pr-5 leading-snug transition duration-200 ease-in-out will-change-transform hover:bg-[#e5d6ff] dark:bg-haiti dark:hover:bg-indigo-900/50"
                        aria-label="Get started with NativePHP documentation for mobile apps"
                        x-on:mouseenter="mobileHover = true"
                        x-on:mouseleave="mobileHover = false"
                    >
                        {{-- Label --}}
                        <div
                            class="flex items-center gap-3 duration-500 ease-in-out will-change-transform group-hover:translate-x-1"
                        >
                            <x-icons.device-mobile-phone
                                class="size-6 shrink-0"
                            />
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

    {{-- Collaborations --}}
    <section
        class="mx-auto mt-20 max-w-5xl px-5"
        aria-labelledby="collaborations-title"
    >
        <h2
            id="collaborations-title"
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [-10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                            },
                        )
                    })
                }
            "
            class="text-center text-xl font-medium capitalize opacity-0"
        >
            A collaboration between:
        </h2>

        {{-- Cards --}}
        <div
            class="mt-5 flex flex-col items-center justify-center gap-5 rounded-2xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] text-center min-[500px]:mt-10 sm:mt-32 sm:flex-row sm:bg-gradient-to-r dark:from-blue-900/10 dark:to-[#4c407f]/25"
            x-data="{ hoverSimon: false, hoverMarcel: false }"
        >
            {{-- Simon card --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [20, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="group/simon flex flex-col-reverse items-center px-2 pt-2 opacity-0 min-[500px]:flex-row min-[500px]:gap-5 sm:-mt-[6.3rem] sm:gap-0 sm:px-0 sm:pt-0 md:gap-5"
                x-on:mouseenter="hoverSimon = true"
                x-on:mouseleave="hoverSimon = false"
            >
                <div class="relative flex flex-col items-center">
                    {{-- Shape --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="hidden w-5 text-[#FFCABA] transition duration-500 ease-in-out will-change-transform group-hover/simon:rotate-90 group-hover/simon:text-orange-300 min-[500px]:block dark:text-orange-300 dark:group-hover/simon:text-orange-400"
                        viewBox="0 0 24 23"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M21.5037 9.50367H18.5162C17.8899 9.50367 17.5764 8.74661 18.0192 8.30376L20.1041 6.21886C20.8478 5.47515 20.9216 4.27314 20.2335 3.47741C19.4666 2.59031 18.1233 2.55376 17.3093 3.36846L15.1969 5.48078C14.7541 5.92362 13.997 5.61012 13.997 4.9838V1.99633C13.997 0.894132 13.1036 0 12.0007 0C10.8985 0 10.0044 0.893429 10.0044 1.99633V4.9838C10.0044 5.61012 9.24731 5.92362 8.80446 5.48078L6.69214 3.36846C5.91259 2.58891 4.64872 2.58891 3.86916 3.36846C3.08961 4.14801 3.08961 5.41189 3.86916 6.19144L5.98148 8.30376C6.42433 8.74661 6.11082 9.50367 5.48451 9.50367H2.49633C1.39413 9.50367 0.5 10.3971 0.5 11.5C0.5 12.6022 1.39343 13.4963 2.49633 13.4963H5.4838C6.11012 13.4963 6.42362 14.2534 5.98078 14.6962L3.86846 16.8086C3.08891 17.5881 3.08891 18.852 3.86846 19.6315C4.64801 20.4111 5.91189 20.4111 6.69144 19.6315L8.80376 17.5192C9.24661 17.0764 10.0037 17.3899 10.0037 18.0162V21.0037C10.0037 22.1059 10.8971 23 12 23C13.1022 23 13.9963 22.1066 13.9963 21.0037V18.0162C13.9963 17.3899 14.7534 17.0764 15.1962 17.5192L17.3086 19.6315C18.0881 20.4111 19.352 20.4111 20.1315 19.6315C20.9111 18.852 20.9111 17.5881 20.1315 16.8086L18.0192 14.6962C17.5764 14.2534 17.8899 13.4963 18.5162 13.4963H21.5037C22.6059 13.4963 23.5 12.6029 23.5 11.5C23.5 10.3978 22.6066 9.50367 21.5037 9.50367Z"
                            fill="currentColor"
                        />
                    </svg>

                    {{-- Name --}}
                    <h3 class="pt-2 text-xl leading-relaxed">
                        Simon
                        <br />
                        Hamp
                    </h3>

                    {{-- Title --}}
                    <div
                        class="flex flex-col items-center pt-2 min-[500px]:pt-0"
                    >
                        <div
                            class="hidden text-2xl font-light min-[500px]:block"
                            aria-hidden="true"
                        >
                            ~
                        </div>
                        <p class="text-sm text-gray-600 dark:text-white/50">
                            Developer & Artisan —
                            <a
                                href="https://laradevs.com/?ref=nativephp"
                                target="_blank"
                                rel="noopener"
                                class="transition duration-200 hover:text-black dark:hover:text-white"
                                aria-label="Visit LaraDevs website - Simon Hamp's company"
                            >
                                LaraDevs
                            </a>
                        </p>
                    </div>

                    {{-- Dashed line --}}
                    <div
                        class="absolute -right-9 top-2 hidden md:block"
                        aria-hidden="true"
                    >
                        <div
                            class="relative flex items-end text-gray-400 transition duration-500 ease-in-out group-hover/simon:text-black dark:group-hover/simon:text-white"
                        >
                            {{-- Line --}}
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8"
                                viewBox="0 0 133 37"
                                fill="none"
                            >
                                <path
                                    x-init="
                                        () => {
                                            motion.animate(
                                                $el,
                                                {
                                                    strokeDashoffset: [20, 0],
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
                                    d="M0 1H49.5166C52.9323 1 56.1117 2.74339 57.9482 5.62334L74.0518 30.8767C75.8883 33.7566 79.0677 35.5 82.4834 35.5H132.5"
                                    stroke="currentColor"
                                    stroke-width="1.2"
                                    stroke-dasharray="5 5"
                                />
                            </svg>

                            {{-- Arrow --}}
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="relative top-0.5 size-2 rotate-90"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="currentColor"
                                    d="M12 1.67a2.91 2.91 0 0 0-2.492 1.403L1.398 16.61a2.914 2.914 0 0 0 2.484 4.385h16.225a2.914 2.914 0 0 0 2.503-4.371L14.494 3.078A2.92 2.92 0 0 0 12 1.67"
                                />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Image --}}
                <img
                    src="{{ Vite::asset('resources/images/simonhamp_faded.webp') }}"
                    alt="Simon Hamp - Creator of NativePHP and founder of LaraDevs"
                    class="pointer-events-none -ml-10 -mr-10 -mt-5 w-52 transition duration-500 ease-in-out will-change-transform group-hover/simon:-translate-x-1 group-hover/simon:-translate-y-1 group-hover/simon:scale-[1.06] sm:-ml-14 sm:-mr-16 sm:w-64"
                    width="256"
                    height="256"
                    :class="{'grayscale-[70%]': hoverMarcel}"
                />
            </div>
            {{-- Marcel card --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-20, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        })
                    }
                "
                class="group/marcel flex flex-col items-center px-2 pb-5 pt-2 opacity-0 min-[500px]:flex-row min-[500px]:gap-5 min-[500px]:pb-0 sm:-mt-[6.3rem] sm:gap-0 sm:px-0 sm:pt-0 md:gap-5"
                x-on:mouseenter="hoverMarcel = true"
                x-on:mouseleave="hoverMarcel = false"
            >
                {{-- Image --}}
                <img
                    src="{{ Vite::asset('resources/images/marcelpaciot_faded.webp') }}"
                    alt="Marcel Pociot - Creator of NativePHP and CTO of BeyondCode"
                    class="pointer-events-none -ml-10 -mr-10 -mt-5 w-52 transition duration-500 ease-in-out will-change-transform group-hover/marcel:-translate-y-1 group-hover/marcel:translate-x-1 group-hover/marcel:scale-[1.06] sm:-ml-16 sm:-mr-14 sm:w-64"
                    width="256"
                    height="256"
                    :class="{'grayscale-[70%]': hoverSimon}"
                />

                <div class="relative flex flex-col items-center">
                    {{-- Shape --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="hidden w-5 text-[#CBDAFF] transition duration-500 ease-in-out will-change-transform group-hover/marcel:-rotate-90 group-hover/marcel:text-blue-300 min-[500px]:block dark:text-rose-300 dark:group-hover/marcel:text-rose-400"
                        viewBox="0 0 24 23"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M12.8747 12.3751C27.0405 26.5408 -3.04242 26.5408 11.1233 12.3751C-3.04242 26.5408 -3.04242 -3.54205 11.1233 10.6237C-3.04242 -3.54205 27.0405 -3.54205 12.8747 10.6237C27.0405 -3.54205 27.0405 26.5408 12.8747 12.3751Z"
                            fill="currentColor"
                        />
                    </svg>

                    {{-- Name --}}
                    <h3 class="pt-2 text-xl leading-relaxed">
                        Marcel
                        <br />
                        Pociot
                    </h3>

                    {{-- Title --}}
                    <div
                        class="flex flex-col items-center pt-2 min-[500px]:pt-0"
                    >
                        <div
                            class="hidden text-2xl font-light min-[500px]:block"
                            aria-hidden="true"
                        >
                            ~
                        </div>
                        <p class="text-sm text-gray-600 dark:text-white/50">
                            CTO & Cofounder —
                            <a
                                href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp"
                                target="_blank"
                                rel="noopener"
                                class="transition duration-200 hover:text-black dark:hover:text-white"
                                aria-label="Visit BeyondCode website - Marcel Pociot's company"
                            >
                                BeyondCode
                            </a>
                        </p>
                    </div>

                    {{-- Dashed line --}}
                    <div
                        class="absolute -left-9 top-2 hidden md:block"
                        aria-hidden="true"
                    >
                        <div
                            class="relative flex items-end text-gray-400 transition duration-500 ease-in-out group-hover/marcel:text-black dark:group-hover/marcel:text-white"
                        >
                            {{-- Arrow --}}
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="relative top-0.5 size-2 -rotate-90"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="currentColor"
                                    d="M12 1.67a2.91 2.91 0 0 0-2.492 1.403L1.398 16.61a2.914 2.914 0 0 0 2.484 4.385h16.225a2.914 2.914 0 0 0 2.503-4.371L14.494 3.078A2.92 2.92 0 0 0 12 1.67"
                                />
                            </svg>
                            {{-- Line --}}
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8"
                                viewBox="0 0 133 37"
                                fill="none"
                            >
                                <path
                                    x-init="
                                        () => {
                                            motion.animate(
                                                $el,
                                                {
                                                    strokeDashoffset: [20, 0],
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
                                    d="M132.5 1H82.9834C79.5677 1 76.3883 2.74339 74.5518 5.62334L58.4482 30.8767C56.6117 33.7566 53.4323 35.5 50.0166 35.5H0"
                                    stroke="currentColor"
                                    stroke-width="1.2"
                                    stroke-dasharray="5 5"
                                />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h3
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $el,
                            {
                                opacity: [0, 1],
                                y: [10, 0],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                            },
                        )
                    })
                }
            "
            class="mt-10 text-center text-xl font-medium capitalize opacity-0"
        >
            + Many community contributors:
        </h3>

        {{-- Contributors List --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $refAll('contributor'),
                            {
                                y: [10, 0],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                ease: motion.backOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
            class="flex flex-wrap items-center justify-center gap-1.5 pt-4"
            aria-label="Community contributors to the NativePHP project"
        >
            <a
                x-ref="contributor"
                href="https://github.com/SRWieZ"
                target="_blank"
                rel="noopener"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0 dark:ring-1 dark:ring-white/10"
                aria-label="View Eser DENIZ's GitHub profile - NativePHP contributor"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/1408020?v=4"
                    alt="Eser DENIZ - NativePHP contributor"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-110"
                    loading="lazy"
                    width="48"
                    height="48"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/XbNz"
                target="_blank"
                rel="noopener"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0 dark:ring-1 dark:ring-white/10"
                aria-label="View A G's GitHub profile - NativePHP contributor"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/12668624?v=4"
                    alt="A G - NativePHP contributor"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-110"
                    loading="lazy"
                    width="48"
                    height="48"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/gwleuverink"
                target="_blank"
                rel="noopener"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0 dark:ring-1 dark:ring-white/10"
                aria-label="View Willem Leuverink's GitHub profile - NativePHP contributor"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/17123491?v=4"
                    alt="Willem Leuverink - NativePHP contributor"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-110"
                    loading="lazy"
                    width="48"
                    height="48"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/PeteBishwhip"
                target="_blank"
                rel="noopener"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0 dark:ring-1 dark:ring-white/10"
                aria-label="View Peter Bishop's GitHub profile - NativePHP contributor"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/9081809?v=4"
                    alt="Peter Bishop - NativePHP contributor"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-110"
                    loading="lazy"
                    width="48"
                    height="48"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/NativePHP/laravel/graphs/contributors"
                target="_blank"
                rel="noopener"
                class="group relative z-0 grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
                aria-label="View all additional contributors to the NativePHP project on GitHub"
            >
                <div
                    class="z-10 self-center justify-self-center truncate text-center text-sm font-medium [grid-area:1/-1]"
                >
                    40+
                </div>
                <svg
                    class="-z-10 h-full w-full self-center justify-self-center [grid-area:1/-1]"
                    viewBox="0 0 63 64"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect
                        class="text-[#C4FEE8] transition duration-300 ease-out group-hover:text-[#aeffe0] dark:text-emerald-500/30 dark:group-hover:text-emerald-500/30"
                        x="0.75"
                        y="1.25"
                        width="61.5"
                        height="61.5"
                        rx="30.75"
                        fill="currentColor"
                    />
                    <rect
                        x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        rotate: [0, 180],
                                    },
                                    {
                                        duration: 6,
                                        repeat: Infinity,
                                        repeatType: 'loop',
                                        ease: 'linear',
                                    },
                                )
                            }
                        "
                        class="origin-center text-[#8CDDBF] dark:opacity-50"
                        x="0.75"
                        y="1.25"
                        width="61.5"
                        height="61.5"
                        rx="30.75"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-dasharray="7 7"
                    />
                </svg>
            </a>
        </div>
    </section>

    {{-- Marcel talk --}}
    <section
        class="mx-auto mt-20 max-w-5xl px-5"
        aria-labelledby="laracon-talk-title"
    >
        <div
            class="flex flex-col items-center gap-5 lg:flex-row lg:justify-between"
        >
            {{-- Left side --}}
            <div class="text-center lg:max-w-96 lg:text-left">
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                    },
                                )
                            })
                        }
                    "
                    class="inline-block rounded-full px-3 py-1 text-sm font-medium uppercase ring-1 ring-black dark:ring-white/15"
                >
                    Laracon US Talk
                </div>
                <h2
                    id="laracon-talk-title"
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                    },
                                )
                            })
                        }
                    "
                    class="pt-2.5 text-xl font-medium capitalize opacity-0"
                >
                    Where did this come from?
                </h2>

                <p
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                    },
                                )
                            })
                        }
                    "
                    class="pt-1.5 leading-relaxed text-gray-500 opacity-0 dark:text-gray-400"
                >
                    Watch Marcel's original NativePHP talk from Laracon US 2023
                    in Nashville. Minds were blown as he demonstrated how to use
                    Laravel to build cross-platform desktop applications.
                </p>
            </div>

            {{-- Right side --}}
            <div class="grid place-items-center">
                <a
                    href="https://www.youtube.com/watch?v=iG7VscBFnqo"
                    target="_blank"
                    rel="noopener"
                    class="group relative"
                    title="Marcel Pociot at Laracon US - Building Desktop Applications with PHP"
                    aria-label="Watch Marcel Pociot's talk at Laracon US 2023"
                >
                    {{-- Play button --}}
                    <div
                        class="absolute right-1/2 top-1/2 grid size-16 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
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
                        src="{{ Vite::asset('resources/images/marcel2023laraconus.webp') }}"
                        alt="Marcel Pociot at Laracon US - Building Desktop Applications with PHP"
                        class="w-full max-w-[505px] rounded-2xl ring-1 ring-black/10"
                        width="505"
                        height="284"
                        loading="lazy"
                    />
                </a>
            </div>
        </div>
    </section>

    {{-- Sponsors --}}
    <section
        class="mx-auto mt-20 max-w-5xl px-5"
        aria-labelledby="sponsors-title"
    >
        <h2
            id="sponsors-title"
            class="sr-only"
        >
            NativePHP Sponsors
        </h2>
        <div class="divide-y divide-[#242A2E]/20 *:py-8">
            {{-- Featured sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-x-10 gap-y-5 md:flex-row md:items-start"
                aria-labelledby="featured-sponsors-title"
            >
                <h3
                    id="featured-sponsors-title"
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                    },
                                )
                            })
                        }
                    "
                    class="shrink-0 text-xl font-medium opacity-0"
                >
                    Featured Sponsors
                </h3>
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $refAll('sponsor'),
                                    {
                                        scale: [0, 1],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.backOut,
                                        delay: motion.stagger(0.1),
                                    },
                                )
                            })
                        }
                    "
                    class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                    aria-label="Featured sponsors of the NativePHP project"
                >
                    <x-sponsors.lists.home.featured-sponsors />
                </div>
            </div>
            {{-- Corporate sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-x-10 gap-y-5 md:flex-row md:items-start"
                aria-labelledby="corporate-sponsors-title"
            >
                <h3
                    id="corporate-sponsors-title"
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                    },
                                )
                            })
                        }
                    "
                    class="shrink-0 text-xl font-medium opacity-0"
                >
                    Corporate Sponsors
                </h3>
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $refAll('sponsor'),
                                    {
                                        scale: [0, 1],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.backOut,
                                        delay: motion.stagger(0.1),
                                    },
                                )
                            })
                        }
                    "
                    class="flex grow flex-wrap items-center justify-center gap-5 md:justify-end"
                    aria-label="Corporate sponsors of the NativePHP project"
                >
                    <x-sponsors.lists.home.corporate-sponsors />
                </div>
            </div>
        </div>
        <div
            x-init="
                () => {
                    motion.inView(
                        $el,
                        (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    x: [-50, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            )
                        },
                        {
                            amount: 0.5,
                        },
                    )
                }
            "
            class="opacity-0 will-change-transform"
        >
            <a
                href="/docs/getting-started/sponsoring"
                class="group flex flex-wrap items-center justify-center gap-x-5 gap-y-3 rounded-3xl bg-gray-100 px-8 py-8 transition duration-200 ease-in-out hover:ring-1 hover:ring-black/60 md:justify-between md:px-12 md:py-10 dark:bg-mirage dark:hover:bg-haiti dark:hover:ring-cloud"
                aria-label="Learn about sponsoring the NativePHP project"
            >
                <div
                    class="inline-flex shrink-0 flex-col-reverse items-center gap-x-5 gap-y-3 md:flex-row"
                >
                    <div class="space-y-2 text-2xl font-medium">
                        <span>Want</span>
                        <span>your logo</span>
                        <span>here?</span>
                    </div>
                    {{-- Arrow --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        class="w-12 -rotate-45 transition duration-300 ease-out will-change-transform group-hover:-translate-y-0.5 group-hover:translate-x-0.5 md:w-16 md:rotate-0"
                        aria-hidden="true"
                    >
                        <path
                            class="text-[#e8e4f8] dark:text-[#31416e]/30"
                            fill="currentColor"
                            d="M12 22c5.5228 0 10 -4.4772 10 -10 0 -5.52285 -4.4772 -10 -10 -10C6.47715 2 2 6.47715 2 12c0 5.5228 4.47715 10 10 10Z"
                            stroke-width="1"
                        ></path>
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.499 2.49707h7v7"
                            stroke-width="1"
                        ></path>
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.499 2.49707 5.49902 18.4971"
                            stroke-width="1"
                        ></path>
                    </svg>
                </div>
                <div
                    class="text-center font-light opacity-50 md:max-w-xs md:text-left md:text-lg"
                >
                    Become a sponsor and get your logo on our README on GitHub
                    with a link to your site.
                </div>
            </a>
        </div>
    </section>
</x-layout>
