<section
    class="mt-2"
    aria-labelledby="hero-title"
    role="region"
>
    <div
        class="relative z-0 flex flex-col overflow-hidden rounded-2xl bg-gradient-to-t from-[#E0E5EB] to-[#F9F9F9] px-5 pt-8 pb-10 ring-1 ring-zinc-200/50 lg:px-10 lg:pt-8 lg:pb-17 xl:pt-10 dark:from-slate-950 dark:to-slate-900 dark:ring-slate-800"
    >
        {{-- Demo app --}}
        <div
            class="order-last mt-7 flex justify-center text-xs lg:order-first lg:mt-0 lg:-mb-20 lg:justify-end 2xl:text-sm"
        >
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                Array.from($el.children),
                                { x: -10, autoAlpha: 0 },
                                {
                                    x: 0,
                                    autoAlpha: 1,
                                    stagger: 0.1,
                                    duration: 0.7,
                                    ease: 'power2.out',
                                },
                            )
                        })
                    }
                "
                class="flex flex-wrap justify-center gap-2 lg:flex-col lg:items-end lg:gap-1.5"
            >
                <p
                    class="w-full text-center font-light lg:w-auto dark:font-extralight"
                >
                    Try our
                    <span class="font-medium">Demo</span>
                    app:
                </p>
                <div>
                    <a
                        href="https://play.google.com/store/apps/details?id=com.nativephp.kitchensinkapp"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 rounded-xl bg-white/70 px-3 py-2.5 backdrop-blur-md transition duration-200 will-change-transform hover:scale-98 hover:bg-white dark:bg-slate-500/25 dark:hover:bg-slate-500/40"
                    >
                        <x-icons.play-store
                            class="h-4.5 2xl:h-5"
                            aria-hidden="true"
                        />
                        <div>Play Store</div>
                    </a>
                </div>
                <div>
                    <a
                        href="https://testflight.apple.com/join/vm9Qtshy"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 rounded-xl bg-white/70 px-3 py-2.5 backdrop-blur-md transition duration-200 will-change-transform hover:scale-98 hover:bg-white dark:bg-slate-500/25 dark:hover:bg-slate-500/40"
                    >
                        <x-icons.app-store
                            class="h-4.5 2xl:h-5"
                            aria-hidden="true"
                        />
                        <div>TestFlight</div>
                    </a>
                </div>
                <div>
                    <a
                        href="https://github.com/nativephp"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 rounded-xl bg-white/70 px-3 py-2.5 backdrop-blur-md transition duration-200 will-change-transform hover:scale-98 hover:bg-white dark:bg-slate-500/25 dark:hover:bg-slate-500/40"
                    >
                        <x-icons.github
                            class="h-4.5 2xl:h-5"
                            aria-hidden="true"
                        />
                        <div>Source code</div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Mockups --}}
        <div class="relative -z-16 flex flex-col-reverse gap-7">
            <div class="flex items-end justify-center">
                <div class="grid w-100 2xl:w-125">
                    {{-- Macbook --}}
                    <img
                        src="{{ Vite::asset('resources/images/home/macbook.webp') }}"
                        alt=""
                        aria-hidden="true"
                        class="relative z-0 w-full self-center justify-self-center [grid-area:1/-1] dark:brightness-80 dark:contrast-150"
                        width="400"
                        height="250"
                        loading="lazy"
                    />
                    {{-- Window --}}
                    <div
                        class="2xs:scale-75 xs:scale-100 relative -top-3 z-1 flex h-40 w-70 scale-60 flex-col self-center justify-self-center overflow-hidden rounded-md [grid-area:1/-1] 2xl:scale-120"
                    >
                        {{-- Header --}}
                        <div
                            class="relative flex h-3 items-center bg-gray-800 px-1"
                        >
                            {{-- Traffic lights --}}
                            <div class="flex items-center gap-[3px]">
                                <div
                                    class="size-[3px] rounded-full bg-green-400"
                                ></div>
                                <div
                                    class="size-[3px] rounded-full bg-amber-400"
                                ></div>
                                <div
                                    class="size-[3px] rounded-full bg-red-400"
                                ></div>
                            </div>
                            {{-- Label --}}
                            <div
                                class="absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2 text-[5px] text-gray-300"
                            >
                                NativePHP App
                            </div>
                        </div>
                        {{-- Page --}}
                        <div
                            class="grid grow place-items-center bg-[#fdfdfc] dark:bg-[#0a0a0a]"
                        >
                            <img
                                src="{{ Vite::asset('resources/images/home/laravel_welcome_light.webp') }}"
                                alt=""
                                aria-hidden="true"
                                class="w-55 self-center justify-self-center [grid-area:1/-1] dark:opacity-0"
                            />
                            <img
                                src="{{ Vite::asset('resources/images/home/laravel_welcome_dark.webp') }}"
                                alt=""
                                aria-hidden="true"
                                class="w-55 self-center justify-self-center opacity-0 [grid-area:1/-1] dark:opacity-100"
                            />
                        </div>
                    </div>
                </div>

                {{-- Iphone --}}
                <img
                    src="{{ Vite::asset('resources/images/home/iphone.webp') }}"
                    alt=""
                    aria-hidden="true"
                    class="relative z-2 -ml-15 w-18 sm:-ml-18 sm:w-23 2xl:-ml-25 2xl:w-30 dark:brightness-80 dark:contrast-150"
                    width="92"
                    height="190"
                    loading="lazy"
                />
            </div>

            {{-- Feature list (infinite vertical marquee) --}}
            @php
                $features = [
                    ['icon' => 'icons.home.share-link', 'label' => 'Native sharing'],
                    ['icon' => 'icons.home.gallery', 'label' => 'Gallery'],
                    ['icon' => 'icons.home.camera', 'label' => 'Camera'],
                    ['icon' => 'icons.home.fingerprint', 'label' => 'Biometrics'],
                    ['icon' => 'icons.home.bell', 'label' => 'Push notifications'],
                    ['icon' => 'icons.home.phone-message', 'label' => 'Native dialogs'],
                    ['icon' => 'icons.home.external-link', 'label' => 'Deep links'],
                    ['icon' => 'icons.home.phone-vibrate', 'label' => 'Haptic feedback'],
                    ['icon' => 'icons.home.flashlight', 'label' => 'Flashlight'],
                    ['icon' => 'icons.home.database-shield', 'label' => 'Secure storage'],
                    ['icon' => 'icons.home.location-pin', 'label' => 'Location services'],
                ];
            @endphp

            {{-- Local CSS for marquee (kept tiny and scoped) --}}
            <style>
                @keyframes nphp-vmarquee {
                    to {
                        transform: translateY(-50%);
                    }
                }
                @keyframes nphp-hmarquee {
                    to {
                        transform: translateX(-50%);
                    }
                }
                .nphp-marquee-track {
                    animation: nphp-vmarquee var(--marquee-duration, 22s) linear
                        infinite;
                }
                .nphp-hmarquee-track {
                    animation: nphp-hmarquee var(--marquee-duration, 18s) linear
                        infinite;
                }
                @media (prefers-reduced-motion: reduce) {
                    .nphp-marquee-track {
                        animation: none !important;
                    }
                    .nphp-hmarquee-track {
                        animation: none !important;
                    }
                }
            </style>

            <div
                class="group absolute top-0 left-5 hidden h-60 overflow-hidden mask-y-from-75% lg:block xl:left-1/7 2xl:left-1/7 2xl:h-75"
            >
                {{-- Track (two sets for seamless loop) --}}
                <div
                    class="nphp-marquee-track flex flex-col gap-3 will-change-transform [--icon-bg:#D3EDF1] [--icon-dot:#F9F9F9] [--icon-stroke:#4197A5] group-hover:[animation-play-state:paused] dark:[--icon-bg:--alpha(var(--color-slate-400)/30%)] dark:[--icon-dot:--alpha(var(--color-white)/30%)] dark:[--icon-stroke:--alpha(var(--color-white)/60%)]"
                    style="--marquee-duration: 24s"
                >
                    {{-- Set A --}}
                    <div class="flex flex-col gap-3">
                        @foreach ($features as $feature)
                            <div
                                class="flex items-center gap-2 text-sm 2xl:text-base"
                            >
                                <x-dynamic-component
                                    :component="$feature['icon']"
                                    class="size-5 2xl:size-6"
                                    aria-hidden="true"
                                />
                                <div class="text-gray-700 dark:text-slate-300">
                                    {{ $feature['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Set B (clone) --}}
                    <div
                        class="flex flex-col gap-3"
                        aria-hidden="true"
                    >
                        @foreach ($features as $feature)
                            <div
                                class="flex items-center gap-2 text-sm 2xl:text-base"
                            >
                                <x-dynamic-component
                                    :component="$feature['icon']"
                                    class="size-5 2xl:size-6"
                                    aria-hidden="true"
                                />
                                <div class="text-gray-700 dark:text-slate-300">
                                    {{ $feature['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Feature list (horizontal marquee on small screens) --}}
            <div
                class="group z-1 block w-full overflow-hidden mask-x-from-70% lg:hidden"
            >
                {{-- Track (two sets for seamless loop) --}}
                <div
                    class="nphp-hmarquee-track flex items-center gap-3 will-change-transform [--icon-bg:#D3EDF1] [--icon-dot:#F9F9F9] [--icon-stroke:#4197A5] group-hover:[animation-play-state:paused] dark:[--icon-bg:--alpha(var(--color-slate-400)/30%)] dark:[--icon-dot:--alpha(var(--color-white)/30%)] dark:[--icon-stroke:--alpha(var(--color-white)/60%)]"
                    style="--marquee-duration: 18s"
                >
                    {{-- Set A --}}
                    <div class="flex items-center gap-3 whitespace-nowrap">
                        @foreach ($features as $feature)
                            <div
                                class="flex items-center gap-2 text-sm 2xl:text-base"
                            >
                                <x-dynamic-component
                                    :component="$feature['icon']"
                                    class="size-5 2xl:size-6"
                                    aria-hidden="true"
                                />
                                <div class="text-gray-700 dark:text-slate-300">
                                    {{ $feature['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Set B (clone) --}}
                    <div
                        class="flex items-center gap-3 whitespace-nowrap"
                        aria-hidden="true"
                    >
                        @foreach ($features as $feature)
                            <div
                                class="flex items-center gap-2 text-sm 2xl:text-base"
                            >
                                <x-dynamic-component
                                    :component="$feature['icon']"
                                    class="size-5 2xl:size-6"
                                    aria-hidden="true"
                                />
                                <div class="text-gray-700 dark:text-slate-300">
                                    {{ $feature['label'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Main --}}
        <div class="mt-12 grid place-items-center text-center text-pretty">
            {{-- Headline --}}
            <h1
                id="hero-title"
                x-init="
                    () => {
                        gsap.fromTo(
                            $el,
                            { autoAlpha: 0, x: -10 },
                            {
                                autoAlpha: 1,
                                x: 0,
                                duration: 1,
                                ease: 'power1.out',
                            },
                        )
                    }
                "
                class="xs:text-3xl 2xs:text-2xl relative text-xl font-bold text-gray-700 lg:text-4xl 2xl:text-5xl dark:text-white"
            >
                Build

                <!-- display: inline -->
                <span
                    class="rounded-2xl bg-gradient-to-tl from-[#AADEE9] to-[#BDE7F0] px-2.5 py-1 text-[#589baa] dark:from-cyan-950 dark:to-cyan-800 dark:text-cyan-400"
                    >Native PHP</span
                >
                Apps

                {{-- Star --}}
                <div
                    x-init="
                        () => {
                            gsap.fromTo(
                                $el,
                                { scale: 0, autoAlpha: 0 },
                                {
                                    scale: 1,
                                    autoAlpha: 1,
                                    duration: 1,
                                    ease: 'back.out(1.4)',
                                },
                            )
                        }
                    "
                    class="absolute -top-4 -left-4"
                >
                    <x-icons.star
                        x-init="
                            () => {
                                gsap.to($el, {
                                    rotate: 180,
                                    duration: 3,
                                    repeat: -1,
                                    ease: 'linear',
                                })
                            }
                        "
                        class="size-4 text-gray-500"
                        aria-hidden="true"
                    />
                </div>

                {{-- Video --}}
                <div
                    x-init="
                        () => {
                            gsap.fromTo(
                                $el,
                                { y: -10, x: 10 },
                                {
                                    y: 0,
                                    x: 0,
                                    duration: 1.5,
                                    ease: 'circ.out',
                                },
                            )
                        }
                    "
                    class="group absolute -top-35 -right-65 hidden items-end gap-1 text-left lg:flex 2xl:-top-39"
                >
                    <div
                        class="relative top-0.5 -mr-6 flex items-end gap-1 2xl:-top-1"
                    >
                        {{-- Black circle --}}
                        <div
                            x-init="
                                () => {
                                    gsap.fromTo(
                                        $el,
                                        { scale: 0 },
                                        {
                                            scale: 1,
                                            duration: 1,
                                            ease: 'back.out(1.4)',
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
                                        gsap.fromTo(
                                            $el,
                                            { strokeDashoffset: 0 },
                                            {
                                                strokeDashoffset: 20,
                                                duration: 1.5,
                                                repeat: -1,
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
                            rel="noopener noreferrer"
                            class="relative -top-5 grid size-10 place-items-center rounded-full bg-black/20 text-white ring-1 ring-white/10 backdrop-blur-sm transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-[#d4fd7d] dark:bg-slate-500/20 dark:group-hover:text-[#9c90f0]"
                            aria-label="Watch NativePHP introduction video on YouTube"
                        >
                            <x-icons.play-button
                                x-init="
                                        () => {
                                            gsap.to($el, {
                                                x: 1,
                                                duration: 0.6,
                                                repeat: -1,
                                                yoyo: true,
                                                ease: 'power1.inOut',
                                            })
                                        }
                                    "
                                class="size-4"
                                aria-hidden="true"
                            />
                            <span class="sr-only">Play introduction</span>
                        </a>
                    </div>
                    <div>
                        <div
                            class="text-xs font-normal text-gray-600 2xl:text-sm dark:text-white/50"
                        >
                            Introducing
                        </div>
                        <div class="text-sm font-medium">
                            NativePHP for Mobile
                        </div>
                        {{-- Image --}}
                        <a
                            href="https://www.youtube.com/watch?v=WOTSjPFXQ2k"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="Watch Simon Hamp's Laracon EU talk about building mobile apps with PHP"
                        >
                            <img
                                src="{{ Vite::asset('resources/images/home/video_introduction_thumbnail.webp') }}"
                                alt="Simon Hamp presenting at Laracon EU 2025 on building mobile apps with PHP"
                                class="mt-2 w-35 rounded-2xl 2xl:w-40"
                                width="140"
                                height="80"
                                loading="lazy"
                            />
                        </a>
                    </div>
                </div>
            </h1>

            {{-- Description --}}
            <p
                x-init="
                    () => {
                        gsap.fromTo(
                            $el,
                            { autoAlpha: 0, y: 10 },
                            {
                                autoAlpha: 1,
                                y: 0,
                                duration: 1,
                                ease: 'power2.out',
                            },
                        )
                    }
                "
                class="xs:text-lg xs:mt-5 mx-auto mt-4 max-w-4xl text-center leading-relaxed text-gray-600 2xl:text-xl dark:text-zinc-400"
                aria-describedby="hero-title"
            >
                Bring your
                <a
                    href="https://www.php.net"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-block font-medium text-gray-900 transition duration-200 will-change-transform hover:-translate-y-0.5 dark:text-white"
                    aria-label="Learn more about PHP programming language"
                >
                    PHP
                </a>
                &
                <a
                    href="https://laravel.com"
                    target="_blank"
                    rel="noopener noreferrer"
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
                        gsap.fromTo(
                            $el,
                            { autoAlpha: 0, x: -10 },
                            {
                                autoAlpha: 1,
                                x: 0,
                                duration: 1,
                                ease: 'power2.out',
                            },
                        )
                    }
                "
                class="mt-4 w-full max-w-55"
            >
                <div class="transition duration-300">
                    <a
                        href="/docs/mobile/1/getting-started/introduction"
                        class="group dark:bg-haiti relative isolate z-0 flex h-15 items-center justify-between gap-3 overflow-hidden rounded-3xl bg-gray-900 px-5 leading-snug text-white transition duration-200 ease-in-out will-change-transform hover:bg-gray-800 2xl:h-17 2xl:px-7 dark:hover:bg-indigo-900/50"
                        aria-label="Get started with NativePHP documentation for mobile apps"
                    >
                        {{-- Label --}}
                        <div
                            class="bg-gradient-to-br from-white to-cyan-300 bg-clip-text text-transparent duration-500 ease-in-out will-change-transform group-hover:translate-x-1 2xl:text-lg"
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
                                    gsap.to($el, {
                                        duration: 10,
                                        repeat: -1,
                                        ease: 'power1.inOut',
                                        keyframes: {
                                            x: [0, 20, -100, 0],
                                            y: [0, 5, 0],
                                            scale: [1, 0.7, 1],
                                            rotate: [0, 10, 0],
                                        },
                                    })
                                }
                            "
                            class="absolute -bottom-12 left-14 -z-10 h-20 w-44 rounded-full bg-transparent blur-xl will-change-transform dark:bg-blue-500/30"
                        ></div>
                        {{-- Orange blur --}}
                        <div
                            x-init="
                                () => {
                                    gsap.to($el, {
                                        duration: 5,
                                        repeat: -1,
                                        ease: 'power1.inOut',
                                        keyframes: {
                                            x: [0, -10, 0],
                                            y: [0, 10, 0],
                                            scale: [1, 1.2, 1],
                                        },
                                    })
                                }
                            "
                            class="absolute -bottom-12 -left-5 -z-20 h-20 w-44 rounded-full bg-transparent blur-xl will-change-transform dark:bg-cyan-500/30"
                        ></div>
                    </a>
                </div>
            </div>

            {{-- Introduction video for mobile viewport --}}
            <div class="mt-6 grid place-items-center lg:hidden">
                <a
                    href="https://www.youtube.com/watch?v=WOTSjPFXQ2k"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group relative"
                    aria-label="Watch introduction to NativePHP for Mobile"
                >
                    {{-- Play button --}}
                    <div
                        class="absolute top-1/2 right-1/2 grid size-16 translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-white ring-1 ring-white/10 backdrop-blur-sm transition duration-300 ease-in-out will-change-transform group-hover:scale-110 group-hover:text-cyan-200"
                        aria-hidden="true"
                    >
                        <x-icons.play-button
                            x-init="
                                () => {
                                    gsap.to($el, {
                                        x: 3,
                                        duration: 0.6,
                                        repeat: -1,
                                        yoyo: true,
                                        ease: 'power1.inOut',
                                    })
                                }
                            "
                            class="size-7"
                            aria-hidden="true"
                        />
                        <span class="sr-only">Play video</span>
                    </div>
                    {{-- Image --}}
                    <img
                        src="{{ Vite::asset('resources/images/home/video_introduction_thumbnail.webp') }}"
                        alt="Introduction to NativePHP for Mobile"
                        class="w-full max-w-sm rounded-2xl"
                        width="350"
                        height="200"
                        loading="lazy"
                    />
                </a>
            </div>
        </div>

        {{-- Top left line --}}
        <div
            class="pointer-events-none absolute top-25 -left-30 -z-17 rotate-45"
            aria-hidden="true"
        >
            <div
                class="h-30 w-120 bg-gradient-to-b from-white/50 to-transparent mask-x-from-70% dark:from-slate-500/5"
            ></div>
        </div>

        {{-- Top right vertical lines --}}
        <div
            class="pointer-events-none absolute top-0 right-0 -z-18 mask-l-from-30%"
            aria-hidden="true"
        >
            <div class="-scale-x-100 -scale-y-100">
                <x-home.vertical-lines />
            </div>
        </div>

        {{-- Bottom left vertical lines --}}
        <div
            class="pointer-events-none absolute bottom-0 left-0 -z-18 mask-r-from-30%"
            aria-hidden="true"
        >
            <x-home.vertical-lines />
        </div>

        {{-- Green blur --}}
        <div
            class="pointer-events-none absolute -top-20 -right-20 -z-19 size-70 rounded-full bg-emerald-100 blur-[100px] dark:bg-emerald-500/20"
            aria-hidden="true"
        ></div>

        {{-- Cyan blur --}}
        <div
            class="pointer-events-none absolute -bottom-50 -left-20 -z-20 size-100 rounded-full bg-cyan-100 blur-[100px] dark:bg-cyan-500/20"
            aria-hidden="true"
        ></div>
    </div>
</section>
