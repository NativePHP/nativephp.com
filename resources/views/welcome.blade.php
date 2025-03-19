<x-layout title="Baking Delicious Native Apps">
    {{-- Hero --}}
    <section class="mt-10 px-5 md:mt-14">
        {{-- Header --}}
        <header
            class="group/header relative isolate grid place-items-center gap-0.5 text-center"
        >
            {{-- Build --}}
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
            >
                Build
            </h1>
            {{-- Native --}}
            <div class="relative isolate">
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
                    class="truncate text-6xl font-extrabold uppercase text-[#9D91F1] min-[400px]:text-7xl md:text-8xl"
                >
                    Native
                </h1>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-20 size-48 rounded-full bg-white/60 blur-[100px] md:-right-32 md:size-60"
                ></div>

                {{-- Star --}}
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 40 40"
                    fill="none"
                    class="absolute -right-10 -top-10 size-8 md:size-10"
                    x-init="
                        () => {
                            motion.animate(
                                $el,
                                {
                                    rotate: [180, 0],
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
                >
                    <path
                        d="M25.66 17.636L40 20L25.66 22.364C23.968 22.644 22.64 23.968 22.364 25.66L20 40L17.636 25.66C17.356 23.968 16.032 22.64 14.34 22.364L0 20L14.34 17.636C16.032 17.356 17.36 16.032 17.636 14.34L20 0L22.364 14.34C22.644 16.032 23.968 17.36 25.66 17.636Z"
                        fill="#E8E4F8"
                    />
                </svg>

                {{-- Glass shape --}}
                <div
                    class="absolute -left-[3rem] top-[4rem] size-6 rounded-bl-xl rounded-br-3xl rounded-tl-3xl rounded-tr-xl bg-[#5A31FF]/10 ring-1 ring-white/50 backdrop-blur-sm min-[400px]:-left-[3.5rem] min-[400px]:top-[4.5rem] min-[400px]:size-8 md:-left-[4.5rem] md:top-[5.6rem] md:size-10"
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
                    class="group absolute -right-[19rem] -top-[5.7rem] hidden items-end gap-1 text-right text-sm lg:flex"
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
                            class="-mb-1.5 size-1 rounded-full bg-white ring-[3px] ring-black"
                        ></div>
                        {{-- Line --}}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="94"
                            height="41"
                            viewBox="0 0 94 41"
                            fill="none"
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
                                stroke="black"
                                stroke-dasharray="5 5"
                            />
                        </svg>
                        {{-- Play button --}}
                        <a
                            href="https://www.youtube.com/watch?v=CsM66a0koAM"
                            target="_blank"
                            class="relative -top-5 grid size-10 place-items-center rounded-full bg-black/30 text-white ring-1 ring-white/10 backdrop-blur transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-4"
                                viewBox="0 0 17 22"
                                fill="none"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M3.69143 0.285087C2.05005 -0.569218 0 0.584286 0 2.57588V19.4241C0 21.4158 2.05005 22.5692 3.69143 21.7149C5.89832 20.5663 9.15122 18.7792 11.8609 16.9047C13.2129 15.9695 14.4582 14.9932 15.3743 14.0457C15.8326 13.5718 16.228 13.0853 16.5129 12.5954C16.7949 12.1104 17 11.5686 17 11C17 10.4314 16.7949 9.88956 16.5129 9.40462C16.228 8.91473 15.8326 8.42821 15.3743 7.95433C14.4582 7.00681 13.2129 6.03045 11.8609 5.09525C9.15122 3.22087 5.89832 1.43373 3.69143 0.285087Z"
                                    fill="currentColor"
                                />
                            </svg>
                        </a>
                    </div>
                    <div>
                        <h4 class="font-medium">Video</h4>
                        <h4 class="font-normal text-gray-600">Introduction</h4>
                        {{-- Image --}}
                        <a
                            href="https://www.youtube.com/watch?v=CsM66a0koAM"
                            target="_blank"
                        >
                            <img
                                src="{{ Vite::asset('resources/images/simon2025laraconeu.webp') }}"
                                alt="Laracon EU 2025 : Simon Hamp // Building Mobile Apps with PHP"
                                class="mt-2 w-40 rounded-xl"
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
            >
                PHP Apps
            </h1>

            {{-- Shiny line --}}
            <div
                class="absolute left-1/2 top-32 z-20 -translate-x-1/2 rotate-[50deg] transition duration-500 ease-out will-change-transform group-hover/header:translate-x-[-55%]"
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
                    class="h-2.5 w-[26rem] bg-gradient-to-r from-transparent to-white/50 ring-1 ring-white/50"
                ></div>
            </div>
        </header>

        {{-- Simon talk for mobile --}}
        <div class="grid place-items-center pt-4 lg:hidden">
            <a
                href="https://www.youtube.com/watch?v=CsM66a0koAM"
                target="_blank"
                class="group relative"
            >
                {{-- Play button --}}
                <div
                    class="absolute right-1/2 top-1/2 grid size-16 -translate-y-1/2 translate-x-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d]"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-8"
                        viewBox="0 0 17 22"
                        fill="none"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M3.69143 0.285087C2.05005 -0.569218 0 0.584286 0 2.57588V19.4241C0 21.4158 2.05005 22.5692 3.69143 21.7149C5.89832 20.5663 9.15122 18.7792 11.8609 16.9047C13.2129 15.9695 14.4582 14.9932 15.3743 14.0457C15.8326 13.5718 16.228 13.0853 16.5129 12.5954C16.7949 12.1104 17 11.5686 17 11C17 10.4314 16.7949 9.88956 16.5129 9.40462C16.228 8.91473 15.8326 8.42821 15.3743 7.95433C14.4582 7.00681 13.2129 6.03045 11.8609 5.09525C9.15122 3.22087 5.89832 1.43373 3.69143 0.285087Z"
                            fill="currentColor"
                        />
                    </svg>
                </div>
                {{-- Image --}}
                <img
                    src="{{ Vite::asset('resources/images/simon2025laraconeu.webp') }}"
                    alt="Laracon EU 2025 : Simon Hamp // Building Mobile Apps with PHP"
                    class="w-full max-w-80 rounded-xl"
                />
            </a>
        </div>

        {{-- Description --}}
        <h3
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
            class="mx-auto max-w-4xl pt-5 text-center text-lg/relaxed text-gray-600 md:text-xl/relaxed"
        >
            Bring your
            <a
                href="https://www.php.net"
                target="_blank"
                class="inline-block font-medium text-[#7a8bd7] transition duration-200 will-change-transform hover:-translate-y-0.5"
            >
                PHP
            </a>
            &
            <a
                href="https://laravel.com"
                target="_blank"
                class="inline-block font-medium text-[#F53003] transition duration-200 will-change-transform hover:-translate-y-0.5"
            >
                Laravel
            </a>
            skills to the world of
            <span class="text-black">desktop & mobile apps</span>
            . You can build cross-platform applications effortlessly—no extra
            tools, just the stack you love.
        </h3>

        {{-- Button --}}
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
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 15 11"
                            fill="none"
                            class="mt-1 w-5 text-[#DBDAE8] transition duration-300 ease-out group-hover:text-[#9d91f1]"
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
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="size-32 text-black transition duration-500 ease-out will-change-transform group-hover:scale-110 group-hover:text-zinc-900"
                        viewBox="0 0 133 133"
                        fill="none"
                    >
                        <path
                            d="M133 66.5028C133 58.2246 128.093 50.5844 119.798 44.4237C121.305 34.2085 119.374 25.3317 113.518 19.4759C107.663 13.6202 98.7915 11.689 88.5707 13.1967C82.4213 4.9071 74.7811 0 66.5028 0C58.2246 0 50.5844 4.9071 44.4237 13.2023C34.2085 11.6946 25.3317 13.6258 19.4759 19.4816C13.6202 25.3374 11.689 34.2086 13.1967 44.4293C4.9071 50.5787 0 58.2246 0 66.5028C0 74.7811 4.9071 82.4213 13.2023 88.582C11.6946 98.7971 13.6258 107.674 19.4816 113.53C25.3374 119.385 34.2086 121.317 44.4293 119.809C50.5844 128.099 58.2302 133.011 66.5085 133.011C74.7867 133.011 82.4269 128.104 88.5876 119.809C98.8027 121.317 107.68 119.385 113.535 113.53C119.391 107.674 121.322 98.8027 119.815 88.582C128.104 82.4269 133.017 74.7811 133.017 66.5028H133Z"
                            fill="currentColor"
                        />
                    </svg>
                </div>
            </a>
        </div>
    </section>

    {{-- Collaborations --}}
    <div class="mx-auto mt-20 max-w-5xl px-5">
        <h2
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
            A collaborative project of:
        </h2>

        <div
            class="mt-5 flex flex-col items-center justify-center gap-5 rounded-2xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] text-center min-[400px]:mt-10 sm:mt-32 sm:flex-row sm:bg-gradient-to-r"
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
                class="group/simon flex flex-col-reverse items-center px-2 pt-2 opacity-0 min-[400px]:flex-row min-[400px]:gap-5 sm:-mt-[6.3rem] sm:gap-0 sm:px-0 sm:pt-0 md:gap-5"
            >
                <div class="relative flex flex-col items-center">
                    {{-- Shape --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="hidden w-5 text-[#FFCABA] transition duration-500 ease-in-out will-change-transform group-hover/simon:rotate-90 group-hover/simon:text-orange-300 min-[400px]:block"
                        viewBox="0 0 24 23"
                        fill="none"
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
                        class="flex flex-col items-center pt-2 min-[400px]:pt-0"
                    >
                        <div
                            class="hidden text-2xl font-light min-[400px]:block"
                        >
                            ~
                        </div>
                        <h5 class="text-sm text-gray-600">
                            Developer & Artisan —
                            <a
                                href="https://laradevs.com/?ref=nativephp"
                                _target="blank"
                                class="transition duration-200 hover:text-black"
                            >
                                LaraDevs
                            </a>
                        </h5>
                    </div>

                    {{-- Dashed line --}}
                    <div class="absolute -right-9 top-2 hidden md:block">
                        <div
                            class="relative flex items-end text-gray-400 transition duration-500 ease-in-out group-hover/simon:text-black"
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
                    alt="Simon Hamp"
                    class="pointer-events-none -ml-10 -mr-10 -mt-5 w-52 transition duration-500 ease-in-out will-change-transform group-hover/simon:-translate-x-1 group-hover/simon:-translate-y-1 group-hover/simon:scale-[1.06] sm:-ml-14 sm:-mr-16 sm:w-64"
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
                class="group/marcel flex flex-col items-center px-2 pb-5 pt-2 opacity-0 min-[400px]:flex-row min-[400px]:gap-5 min-[400px]:pb-0 sm:-mt-[6.3rem] sm:gap-0 sm:px-0 sm:pt-0 md:gap-5"
            >
                {{-- Image --}}
                <img
                    src="{{ Vite::asset('resources/images/marcelpaciot_faded.webp') }}"
                    alt="Marcel Paciot"
                    class="pointer-events-none -ml-10 -mr-10 -mt-5 w-52 transition duration-500 ease-in-out will-change-transform group-hover/marcel:-translate-y-1 group-hover/marcel:translate-x-1 group-hover/marcel:scale-[1.06] sm:-ml-16 sm:-mr-14 sm:w-64"
                />

                <div class="relative flex flex-col items-center">
                    {{-- Shape --}}
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="hidden w-5 text-[#CBDAFF] transition duration-500 ease-in-out will-change-transform group-hover/marcel:-rotate-90 group-hover/marcel:text-blue-300 min-[400px]:block"
                        viewBox="0 0 24 23"
                        fill="none"
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
                        Paciot
                    </h3>

                    {{-- Title --}}
                    <div
                        class="flex flex-col items-center pt-2 min-[400px]:pt-0"
                    >
                        <div
                            class="hidden text-2xl font-light min-[400px]:block"
                        >
                            ~
                        </div>
                        <h5 class="text-sm text-gray-600">
                            CEO & Cofounder —
                            <a
                                href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=nativephp"
                                _target="blank"
                                class="transition duration-200 hover:text-black"
                            >
                                BeyondCode
                            </a>
                        </h5>
                    </div>

                    {{-- Dashed line --}}
                    <div class="absolute -left-9 top-2 hidden md:block">
                        <div
                            class="relative flex items-end text-gray-400 transition duration-500 ease-in-out group-hover/marcel:text-black"
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

        <h2
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
        </h2>

        {{-- List --}}
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
        >
            <a
                x-ref="contributor"
                href="https://github.com/milwad-dev"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/98118400?v=4"
                    alt="Milwad Khosravi"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/SRWieZ"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/1408020?v=4"
                    alt="Eser DENIZ"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/XbNz"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/12668624?v=4"
                    alt="A G"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/nexxai"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/4316564?v=4"
                    alt="JT Smith"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/Mombuyish"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/8007787?v=4"
                    alt="Yish"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/gwleuverink"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full opacity-0"
            >
                <img
                    src="https://avatars.githubusercontent.com/u/17123491?v=4"
                    alt="Willem Leuverink"
                    class="h-full w-full object-cover transition duration-300 ease-out will-change-transform group-hover:scale-125"
                    loading="lazy"
                />
            </a>
            <a
                x-ref="contributor"
                href="https://github.com/NativePHP/laravel/graphs/contributors"
                target="_blank"
                class="group grid size-12 place-items-center overflow-hidden rounded-full border-[1px] border-dashed border-emerald-600 bg-emerald-100 font-medium opacity-0 transition duration-300 ease-out hover:bg-emerald-200/70"
            >
                +27
            </a>
        </div>
    </div>

    {{-- Marcel talk --}}
    <section class="mx-auto mt-20 max-w-5xl px-5">
        <div class="flex flex-wrap items-center justify-between gap-5">
            <div class="lg:max-w-96">
                <div
                    class="inline rounded-full px-3 py-1 text-xs font-medium uppercase ring-1 ring-black"
                >
                    Laracon US Talk
                </div>
                <h3
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
                    class="pt-3 text-xl font-medium capitalize opacity-0"
                >
                    Want to learn more about the project?
                </h3>

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
                    class="pt-2 text-sm/relaxed text-gray-500 opacity-0"
                >
                    Pociot demonstrates how "NativePHP" streamlines development
                    processes, allowing developers to build desktop applications
                    using minimal abstractions and in the PHP language they
                    know.
                    <br />
                    Through practical examples and thought-provoking insights,
                    this talk empowers developers to embrace simplicity, and
                    leverage the true power of PHP in creating efficient and
                    maintainable desktop applications.
                </p>
            </div>
            <iframe
                width="560"
                height="315"
                src="https://www.youtube.com/embed/iG7VscBFnqo?si=AcavmLM7l_oczik7"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
                class="rounded-xl ring-1 ring-black/10"
            ></iframe>
        </div>
    </section>

    {{-- Sponsors --}}
    <section class="mx-auto mt-20 max-w-5xl px-5">
        <div class="divide-y divide-[#242A2E]/20 *:py-8">
            {{-- Featured sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-10 md:flex-row md:items-start"
            >
                <h2
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
                </h2>
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
                >
                    <x-sponsors.lists.home.featured-sponsors />
                </div>
            </div>
            {{-- Corporate sponsors --}}
            <div
                class="flex flex-col items-center justify-between gap-10 md:flex-row md:items-start"
            >
                <h2
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
                </h2>
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
                class="group flex flex-wrap items-center justify-center gap-x-5 gap-y-3 rounded-3xl bg-gray-100 px-8 py-8 transition duration-200 ease-in-out hover:ring-1 hover:ring-black/60 md:justify-between md:px-12 md:py-10"
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
                    >
                        <path
                            fill="#e8e4f8"
                            d="M12 22c5.5228 0 10 -4.4772 10 -10 0 -5.52285 -4.4772 -10 -10 -10C6.47715 2 2 6.47715 2 12c0 5.5228 4.47715 10 10 10Z"
                            stroke-width="1"
                        ></path>
                        <path
                            stroke="#191919"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.499 2.49707h7v7"
                            stroke-width="1"
                        ></path>
                        <path
                            stroke="#191919"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.499 2.49707 5.49902 18.4971"
                            stroke-width="1"
                        ></path>
                    </svg>
                </div>
                <div
                    class="text-center font-light text-black/70 md:max-w-xs md:text-left md:text-lg"
                >
                    Become a sponsor and get your logo on our README on GitHub
                    with a link to your site.
                </div>
            </a>
        </div>
    </section>
</x-layout>
