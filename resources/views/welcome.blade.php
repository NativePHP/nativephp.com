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
        <div class="grid place-items-center pt-5">
            <a
                x-init="
                    () => {
                        motion.animate(
                            $el,
                            {
                                scale: [0, 1],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.8,
                                ease: motion.backOut,
                            },
                        )
                    }
                "
                href="/docs/"
                class="group isolate z-0 grid place-items-center leading-snug text-white will-change-transform"
                aria-label="Get started with NativePHP documentation"
            >
                {{-- Label --}}
                <div
                    class="z-10 grid place-items-center gap-1.5 self-center justify-self-center [grid-area:1/-1]"
                >
                    <div>Get</div>
                    <div>Started</div>

                    {{-- Arrow --}}
                    <div
                        x-init="
                            () => {
                                motion.animate(
                                    $el,
                                    {
                                        x: [0, 5],
                                    },
                                    {
                                        duration: 0.8,
                                        repeat: Infinity,
                                        repeatType: 'mirror',
                                        ease: motion.easeInOut,
                                    },
                                )
                            }
                        "
                        aria-hidden="true"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 15 11"
                            fill="none"
                            class="mt-1 w-5 text-[#DBDAE8] transition duration-300 ease-out group-hover:text-violet-400"
                            aria-hidden="true"
                        >
                            <path
                                d="M1 4.8C0.613401 4.8 0.3 5.1134 0.3 5.5C0.3 5.8866 0.613401 6.2 1 6.2L1 4.8ZM14.495 5.99498C14.7683 5.72161 14.7683 5.27839 14.495 5.00503L10.0402 0.550253C9.76684 0.276886 9.32362 0.276886 9.05025 0.550253C8.77689 0.823621 8.77689 1.26684 9.05025 1.5402L13.0101 5.5L9.05025 9.4598C8.77689 9.73317 8.77689 10.1764 9.05025 10.4497C9.32362 10.7231 9.76683 10.7231 10.0402 10.4497L14.495 5.99498ZM1 6.2L14 6.2L14 4.8L1 4.8L1 6.2Z"
                                fill="currentColor"
                            />
                        </svg>
                    </div>
                </div>

                {{-- Shape --}}
                <div
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
                    class="self-center justify-self-center [grid-area:1/-1]"
                    aria-hidden="true"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-32 text-black transition duration-500 ease-out will-change-transform group-hover:scale-110 group-hover:text-zinc-900 dark:text-[#181a25] dark:group-hover:text-black"
                        viewBox="0 0 133 133"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M133 66.5028C133 58.2246 128.093 50.5844 119.798 44.4237C121.305 34.2085 119.374 25.3317 113.518 19.4759C107.663 13.6202 98.7915 11.689 88.5707 13.1967C82.4213 4.9071 74.7811 0 66.5028 0C58.2246 0 50.5844 4.9071 44.4237 13.2023C34.2085 11.6946 25.3317 13.6258 19.4759 19.4816C13.6202 25.3374 11.689 34.2086 13.1967 44.4293C4.9071 50.5787 0 58.2246 0 66.5028C0 74.7811 4.9071 82.4213 13.2023 88.582C11.6946 98.7971 13.6258 107.674 19.4816 113.53C25.3374 119.385 34.2086 121.317 44.4293 119.809C50.5844 128.099 58.2302 133.011 66.5085 133.011C74.7867 133.011 82.4269 128.104 88.5876 119.809C98.8027 121.317 107.68 119.385 113.535 113.53C119.391 107.674 121.322 98.8027 119.815 88.582C128.104 82.4269 133.017 74.7811 133.017 66.5028H133Z"
                            fill="currentColor"
                        />
                    </svg>
                </div>

                {{-- Blur --}}
                <div
                    class="hidden size-20 self-center justify-self-center bg-indigo-400/70 blur-3xl [grid-area:1/-1] dark:block"
                    aria-hidden="true"
                ></div>
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
