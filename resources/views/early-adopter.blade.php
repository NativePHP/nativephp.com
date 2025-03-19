<x-layout title="NativePHP for iOS and Android">
    {{-- Hero --}}
    <section class="mt-10 px-5 md:mt-14">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Header --}}
            <h1
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="text-3xl font-extrabold sm:text-4xl"
            >
                NativePHP For Mobile
            </h1>

            {{-- Description --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                Development of NativePHP for mobile has already started and you
                can get access and start building apps right now!
            </h3>
        </header>

        {{-- Cards --}}
        <div class="flex flex-wrap items-center justify-center gap-6 pt-10">
            {{-- iOS --}}
            <div
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#EBECF6] p-8 text-center ring-4 ring-inset ring-white/60 dark:bg-black/50 dark:ring-white/5"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Available on</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">iOS</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm dark:text-gray-400">
                        Join the Early Access Program to start developing iOS
                        apps.
                    </h4>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/ios_phone_mockup.webp') }}"
                            alt=""
                            class="-mb-[10.5rem] w-40 dark:mix-blend-hard-light"
                        />
                    </div>
                    {{-- White blurred circle --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl dark:bg-[#3a3f67]"
                    ></div>
                    {{-- Blue blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#9CA8D9]/40 blur-2xl dark:bg-blue-800/40"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#DDE2F3] blur-[100px] dark:bg-[#444892]/80"
                ></div>
            </div>

            {{-- Android --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#F6F1EB] p-8 text-center ring-4 ring-inset ring-white/60 dark:bg-black/50 dark:ring-white/5"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Coming soon for</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">Android</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm dark:text-gray-400">
                        We're at hard work to make this possible, stay tuned!
                    </h4>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/android_phone_mockup.webp') }}"
                            alt=""
                            class="-mb-[10.5rem] w-40"
                        />
                    </div>
                    {{-- White blurred circle --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl dark:bg-[#3a3f67]"
                    ></div>
                    {{-- Center blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#E0D7CE] blur-2xl dark:bg-slate-700/40"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#FBF2E7] blur-[100px] dark:bg-slate-500/30"
                ></div>
            </div>
        </div>
    </section>

    {{-- Quick instructions --}}
    <section class="mt-20 px-5">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Header --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="text-3xl font-semibold opacity-0"
            >
                Quick instructions
            </h2>

            {{-- Description --}}
            <h3
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0"
            >
                Get your app up and running in minutes.
            </h3>
        </header>

        {{-- Steps --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate([
                            {{-- Slide 1 --}}
                            [
                                $refs.slide1.querySelector('h5'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                },
                            ],
                            [
                                $refs.slide1.querySelector('h6'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: '-0.5',
                                },
                            ],
                            [
                                $refs.slide1.querySelector('[x-ref=box]'),
                                {
                                    opacity: [0, 1],
                                    scale: [0, 1],
                                },
                                {
                                    duration: 0.8,
                                    ease: motion.backOut,
                                    at: '-0.8',
                                },
                            ],
                            [
                                $refs.slide1.querySelector('[x-ref=checkmark]'),
                                {
                                    opacity: [0, 1],
                                    scale: [0, 1],
                                },
                                {
                                    duration: 0.5,
                                    ease: motion.backOut,
                                    at: '-0.5',
                                },
                            ],
                            [
                                $refs.slide1.querySelector('[x-ref=success_title]'),
                                {
                                    opacity: [0, 1],
                                    y: [-10, 0],
                                },
                                {
                                    duration: 0.5,
                                    ease: motion.circOut,
                                    at: '-0.5',
                                },
                            ],
                            [
                                $refs.slide1.querySelector('[x-ref=success_subtitle]'),
                                {
                                    opacity: [0, 0.5],
                                    y: [10, 0],
                                },
                                {
                                    duration: 0.5,
                                    ease: motion.circOut,
                                    at: '-0.5',
                                },
                            ],
                            {{-- Slide 2 --}}
                            [
                                $refs.slide2.querySelector('h5'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: 0.5,
                                },
                            ],
                            [
                                $refs.slide2.querySelector('h6'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: '-0.5',
                                },
                            ],
                            [
                                $refs.slide2.querySelector('[x-ref=box]'),
                                {
                                    opacity: [0, 1],
                                    scale: [0, 1],
                                },
                                {
                                    duration: 0.8,
                                    ease: motion.backOut,
                                    at: '-0.8',
                                },
                            ],
                            [
                                $refs.slide2.querySelector('[x-ref=terminal]'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: '-0.7',
                                },
                            ],
                            [
                                $refs.bashline1,
                                { clipPath: ['inset(0 100% 0 0)', 'inset(0 0% 0 0)'] },
                                {
                                    duration: 1.5,
                                    ease: 'steps(16)',
                                    at: '-0.3',
                                },
                            ],
                            [
                                $refs.bashline2,
                                { clipPath: ['inset(0 100% 0 0)', 'inset(0 0% 0 0)'] },
                                {
                                    duration: 1.8,
                                    ease: 'steps(31)',
                                    at: '-0.8',
                                },
                            ],
                            {{-- Slide 3 --}}
                            [
                                $refs.slide3.querySelector('h5'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: 1,
                                },
                            ],
                            [
                                $refs.slide3.querySelector('h6'),
                                {
                                    opacity: [0, 1],
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.circOut,
                                    at: '-0.5',
                                },
                            ],
                            [
                                $refs.slide3.querySelector('[x-ref=box]'),
                                {
                                    opacity: [0, 1],
                                    scale: [0, 1],
                                },
                                {
                                    duration: 0.8,
                                    ease: motion.backOut,
                                    at: '-0.8',
                                },
                            ],
                        ])
                    })
                }
            "
            class="flex flex-wrap items-center justify-center gap-x-10 gap-y-5 pt-7"
        >
            {{-- Slide 1 --}}
            <div x-ref="slide1">
                {{-- Step number --}}
                <h5 class="font-medium opacity-0">Step 1</h5>
                {{-- Step description --}}
                <h6 class="pt-0.5 text-sm text-gray-500 opacity-0">
                    Buy a license.
                </h6>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="mt-3 grid h-52 w-72 place-items-center rounded-xl bg-[#f4f1ee] p-5 opacity-0 dark:bg-gray-900/40"
                >
                    <div class="flex flex-col items-center gap-5 text-center">
                        {{-- Checkmark --}}
                        <div
                            x-ref="checkmark"
                            class="relative grid size-7 place-items-center rounded-full bg-emerald-400 text-black opacity-0 ring-[9px] ring-emerald-400/20"
                        >
                            <x-icons.checkmark class="size-6" />

                            <div
                                class="absolute right-1/2 top-1/2 hidden size-24 -translate-y-1/2 translate-x-1/2 rounded-full bg-emerald-400/20 blur-2xl dark:block"
                            ></div>
                        </div>
                        {{-- Success message --}}
                        <div class="space-y-1 dark:text-white">
                            <div
                                x-ref="success_title"
                                class="text-sm font-medium opacity-0"
                            >
                                Payment successful!
                            </div>
                            <div
                                x-ref="success_subtitle"
                                class="text-xs opacity-0"
                            >
                                You've purchased a license.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Slide 2 --}}
            <div x-ref="slide2">
                {{-- Step number --}}
                <h5 class="font-medium opacity-0">Step 2</h5>
                {{-- Step description --}}
                <h6 class="pt-0.5 text-sm text-gray-500 opacity-0">
                    Install the private Composer package.
                </h6>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="relative isolate z-0 mt-3 grid h-52 w-72 place-items-center overflow-hidden rounded-xl opacity-0"
                >
                    {{-- Terminal --}}
                    <div
                        x-ref="terminal"
                        class="-mb-12 -mr-12 h-52 w-72 overflow-hidden rounded-xl bg-white opacity-0 dark:bg-gray-900"
                    >
                        <div
                            class="flex items-center gap-1 bg-gray-100 px-3 py-3.5 dark:bg-gray-800"
                        >
                            <div
                                class="size-1.5 rounded-full bg-rose-400"
                            ></div>
                            <div
                                class="size-1.5 rounded-full bg-yellow-400"
                            ></div>
                            <div
                                class="size-1.5 rounded-full bg-green-400"
                            ></div>
                        </div>
                        <div class="space-y-1 p-4 text-xs">
                            <div
                                x-ref="bashline1"
                                class="text-gray-500"
                            >
                                ~/native-php-app
                            </div>
                            <div
                                x-ref="bashline2"
                                class="font-medium"
                            >
                                composer require nativephp/ios
                            </div>
                        </div>
                    </div>
                    {{-- Background image --}}
                    <img
                        src="{{ Vite::asset('resources/images/mobile/macos_wallpaper.webp') }}"
                        alt=""
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                    />
                </div>
            </div>
            {{-- Slide 3 --}}
            <div x-ref="slide3">
                {{-- Step number --}}
                <h5 class="font-medium opacity-0">Step 3</h5>
                {{-- Step description --}}
                <h6 class="pt-0.5 text-sm text-gray-500 opacity-0">
                    Start your app.
                </h6>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="relative isolate z-0 mt-3 grid h-52 w-72 place-items-center overflow-hidden rounded-xl opacity-0"
                >
                    {{-- Background image --}}
                    <img
                        src="{{ Vite::asset('resources/images/mobile/developer_holding_phone.webp') }}"
                        alt=""
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                    />
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section class="mx-auto mt-24 max-w-6xl px-5">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Header --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="text-3xl font-semibold opacity-0"
            >
                Purchase a license
            </h2>

            {{-- Description --}}
            <h3
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0"
            >
                Start your journey to become a mobile developer
            </h3>
        </header>

        {{-- Plans --}}
        <div
            class="grid grid-cols-[repeat(auto-fill,minmax(19rem,1fr))] items-start gap-x-6 gap-y-8 pt-10"
        >
            <div
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gray-100 p-7 opacity-0 dark:bg-gray-900/40"
            >
                {{-- Name --}}
                <h4 class="text-2xl font-semibold">Pro</h4>

                {{-- Price --}}
                <div class="flex items-start gap-1.5 pt-5">
                    <div class="text-5xl font-semibold">
                        ${{ number_format(50) }}
                    </div>
                    <div class="self-end pb-1.5 text-zinc-500">per year</div>
                </div>

                {{-- Warning --}}
                <div class="flex items-center gap-3 pt-3 text-sm">
                    <x-icons.warning class="size-5 shrink-0" />
                    <h6 class="text-zinc-500">
                        The price will bump to
                        <span class="font-medium text-black dark:text-white">
                            $100
                        </span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-gray-900/30 bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-gray-900 dark:hover:bg-slate-700/40"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                1
                            </span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                1
                            </span>
                            developer seats
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-5 h-px w-full rounded-full bg-black/15"></div>

                {{-- Perks --}}
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Business hours support (GMT)</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Your name in NativePHP's history</div>
                    </div>
                </div>
            </div>
            <div
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gray-100 p-7 opacity-0 dark:bg-gray-900/40"
            >
                {{-- Name --}}
                <h4 class="text-2xl font-semibold">Teams</h4>

                {{-- Price --}}
                <div class="flex items-start gap-1.5 pt-5">
                    <div class="text-5xl font-semibold">
                        ${{ number_format(150) }}
                    </div>
                    <div class="self-end pb-1.5 text-zinc-500">per year</div>
                </div>

                {{-- Warning --}}
                <div class="flex items-center gap-3 pt-3 text-sm">
                    <x-icons.warning class="size-5 shrink-0" />
                    <h6 class="text-zinc-500">
                        The price will bump to
                        <span class="font-medium text-black dark:text-white">
                            $1000
                        </span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-gray-900/30 bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-gray-900 dark:hover:bg-slate-700/40"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                10
                            </span>
                            developer seats
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-5 h-px w-full rounded-full bg-black/15"></div>

                {{-- Perks --}}
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Business hours support (GMT)</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        >
                            <x-icons.xmark
                                class="size-2.5 shrink-0 dark:opacity-70"
                            />
                        </div>
                        <div>Your name in NativePHP's history</div>
                    </div>
                </div>
            </div>
            <div
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative rounded-2xl bg-gray-100 p-7 opacity-0 ring-1 ring-black dark:bg-black dark:ring-white/20"
            >
                {{-- Popular badge --}}
                <div
                    class="absolute -right-3 -top-5 rounded-xl bg-gradient-to-tr from-[#6886FF] to-[#B8C1FF] px-5 py-2 text-sm text-white dark:from-[#c0b4ff] dark:to-[#7d6fc3]"
                >
                    Most Popular
                </div>

                {{-- Name --}}
                <h4 class="text-2xl font-semibold">Max</h4>

                {{-- Price --}}
                <div class="flex items-start gap-1.5 pt-5">
                    <div class="text-5xl font-semibold">
                        ${{ number_format(250) }}
                    </div>
                    <div class="self-end pb-1.5 text-zinc-500">per year</div>
                </div>

                {{-- Warning --}}
                <div class="flex items-center gap-3 pt-3 text-sm">
                    <x-icons.warning class="size-5 shrink-0" />
                    <h6 class="text-zinc-500">
                        The price will bump to
                        <span class="font-medium text-black dark:text-white">
                            $2500
                        </span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#c2b5fe] dark:text-black dark:hover:bg-[#ab9bfc]"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span
                                class="font-medium text-black dark:text-white"
                            >
                                Unlimited
                            </span>
                            developer seats
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="my-5 h-px w-full rounded-full bg-black/15"></div>

                {{-- Perks --}}
                <div class="space-y-2.5 text-sm">
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">
                            Business hours support (GMT)
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">
                            Your name in NativePHP's history
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="mx-auto mt-24 max-w-6xl px-5">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Header --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="flex items-center gap-2 rounded-bl-md rounded-br-xl rounded-tl-xl rounded-tr-xl bg-gray-100 py-2 pl-4 pr-5 text-2xl text-gray-800 opacity-0 dark:bg-gray-900 dark:text-white"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5"
                    viewBox="0 0 21 20"
                    fill="none"
                >
                    <path
                        d="M14.25 2.50009C14.2498 1.95806 14.0734 1.43078 13.7475 0.997694C13.4216 0.564609 12.9638 0.249181 12.443 0.0989345C11.9222 -0.0513117 11.3667 -0.0282364 10.8602 0.164683C10.3537 0.357603 9.92358 0.709917 9.63469 1.16854C9.34581 1.62716 9.21379 2.16725 9.25853 2.70743C9.30328 3.24761 9.52235 3.75862 9.88276 4.16346C10.2432 4.5683 10.7254 4.84505 11.2567 4.95199C11.7881 5.05894 12.3398 4.9903 12.8287 4.75642C12.7064 5.52063 12.4924 6.2673 12.1912 6.98025C11.6812 8.17404 10.9475 9.17657 10.0325 10.2116C9.92809 10.3365 9.87652 10.4972 9.88877 10.6595C9.90102 10.8219 9.97612 10.973 10.0981 11.0808C10.2201 11.1886 10.3793 11.2446 10.5419 11.2368C10.7045 11.229 10.8576 11.1581 10.9688 11.0391C11.9275 9.9541 12.7563 8.83781 13.34 7.47026C13.925 6.10022 14.25 4.51391 14.25 2.50009ZM20.5 8.4878V12.8455C20.5 14.618 19.045 16.0531 17.25 16.0531H11.5125L6.49875 19.7544C6.23254 19.9503 5.90015 20.0339 5.57296 19.9874C5.24576 19.9409 4.94985 19.768 4.74875 19.5057C4.58786 19.2934 4.50054 19.0345 4.5 18.7682V16.0531H3.75C1.955 16.0531 0.5 14.6168 0.5 12.8455V4.45766C0.5 2.68634 1.955 1.25004 3.75 1.25004H8.2125C8.075 1.64131 8 2.06257 8 2.50009H3.75C2.63 2.50009 1.75 3.39137 1.75 4.45766V12.8455C1.75 13.9117 2.63 14.803 3.75 14.803H5.75V18.7507H5.75375L5.75625 18.7494L11.1012 14.803H17.25C18.37 14.803 19.25 13.9117 19.25 12.8455V10.5254C19.7125 9.90035 20.1375 9.22908 20.5 8.4878ZM18 6.96169e-08C18.663 6.96169e-08 19.2989 0.263401 19.7678 0.732259C20.2366 1.20112 20.5 1.83702 20.5 2.50009C20.5 4.51266 20.175 6.10022 19.59 7.47026C19.0062 8.83781 18.1775 9.9541 17.2188 11.0391C17.1644 11.1007 17.0985 11.151 17.0247 11.187C16.951 11.2231 16.8708 11.2443 16.7889 11.2494C16.7069 11.2545 16.6248 11.2434 16.5471 11.2168C16.4695 11.1902 16.3978 11.1485 16.3362 11.0941C16.2119 10.9844 16.1363 10.8298 16.126 10.6643C16.1157 10.4988 16.1715 10.3359 16.2812 10.2116C17.1975 9.17657 17.9313 8.17404 18.4412 6.98025C18.7225 6.32022 18.9413 5.5927 19.0788 4.75517C18.7402 4.91723 18.3695 5.00097 17.9941 5.00017C17.6188 4.99938 17.2484 4.91407 16.9105 4.75058C16.5727 4.5871 16.2759 4.34962 16.0424 4.05578C15.8088 3.76194 15.6444 3.41928 15.5614 3.05322C15.4783 2.68716 15.4788 2.30709 15.5627 1.94123C15.6466 1.57536 15.8118 1.23309 16.046 0.939799C16.2803 0.646509 16.5776 0.409733 16.9158 0.24704C17.2541 0.0843465 17.6246 -8.85057e-05 18 6.96169e-08Z"
                        fill="currentColor"
                    />
                </svg>
                <div>Testimonials</div>
            </h2>

            {{-- Description --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0 dark:text-white/50"
            >
                Read what the people say about NativePHP
            </h3>
        </header>

        {{-- List --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            $refAll('testimonial'),
                            {
                                scale: [0, 1],
                                opacity: [0, 1],
                            },
                            {
                                duration: 0.7,
                                ease: motion.circOut,
                                delay: motion.stagger(0.1),
                            },
                        )
                    })
                }
            "
            class="columns-1 pt-2 sm:columns-2 lg:columns-3"
        >
            <x-testimonial
                quote="I have been enjoying NativePHP a lot!"
                author="John Doe"
                handle="@johndoe"
                avatar="https://i.pravatar.cc/200?img=3"
                content="I've been using NativePHP for a while now, and I have to say it's been a great experience. The community is fantastic, and the support is top-notch."
            />

            <x-testimonial
                quote="This framework changed how I build desktop apps!"
                author="Jane Smith"
                handle="@janesmith"
                avatar="https://i.pravatar.cc/200?img=5"
                content="Absolutely incredible tool for creating cross-platform applications with Laravel. The developer experience is top-notch."
            />

            <x-testimonial
                quote="So easy to use and powerful!"
                author="Alex Johnson"
                handle="@alexj"
                avatar="https://i.pravatar.cc/200?img=7"
                content="Finally, a solution that lets me build desktop apps using the Laravel skills I already have. Game changer!"
            />

            <x-testimonial
                quote="I can't wait to see what's next!"
                author="Sarah Brown"
                handle="@sarahb"
                avatar="https://i.pravatar.cc/200?img=9"
                content="NativePHP has been a game-changer for my development workflow. The ease of use and the community support are unparalleled."
            />

            <x-testimonial
                quote="This is the future of desktop app development!"
                author="Michael White"
                handle="@michaelw"
                avatar="https://i.pravatar.cc/200?img=11"
                content="NativePHP has revolutionized how I build desktop applications. The integration with Laravel is seamless and powerful."
            />

            <x-testimonial
                quote="A must-have for any developer!"
                author="Emily Clark"
                handle="@emilyc"
                avatar="https://i.pravatar.cc/200?img=19"
                content="The features and support provided by NativePHP are top-notch. It has significantly improved my productivity."
            />
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mx-auto mt-24 max-w-5xl px-5">
        {{-- Header --}}
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
                                ease: motion.easeOut,
                            },
                        )
                    })
                }
            "
            class="text-center text-3xl font-semibold opacity-0"
        >
            Frequently Asked Questions
        </h2>

        {{-- List --}}
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
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        },
                        {
                            amount: 0.2,
                        },
                    )
                }
            "
            class="mx-auto flex w-full max-w-2xl flex-col items-center gap-4 pt-10 opacity-0 [counter-reset:css-counter_0]"
        >
            <x-faq-card
                question="Can I create both iOS and Android apps with one license?"
            >
                <p>
                    Yes, a single license will let you build apps for both iOS
                    and Android as soon as Android support is released. Stay
                    tuned!
                </p>
            </x-faq-card>
            <x-faq-card question="Can I upgrade or downgrade my license later?">
                <p>
                    Of course! You can easily upgrade or downgrade your license
                    at any time. Just let us know, and we'll handle the rest.
                </p>
            </x-faq-card>
            <x-faq-card question="Will my apps built with NativePHP be secure?">
                <p>
                    Definitely. NativePHP includes built-in protection against
                    tampering and ensures your apps remain secure and reliable.
                </p>
            </x-faq-card>
            <x-faq-card question="Can I try NativePHP before purchasing?">
                <p>
                    Sure thing! You can join our Early Access Program for iOS to
                    see what NativePHP can do before buying a license.
                </p>
            </x-faq-card>
            <x-faq-card question="Can I use NativePHP for commercial projects?">
                <p>
                    Absolutely! You can use NativePHP for any project, including
                    commercial ones. We can't wait to see what you build!
                </p>
            </x-faq-card>
        </div>
    </section>

    {{-- Why join the program --}}
    <section class="mx-auto mt-20 max-w-5xl px-5">
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
                                    x: [-10, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        },
                        {
                            amount: 0.2,
                        },
                    )
                }
            "
            class="prose mx-auto max-w-2xl opacity-0 dark:text-gray-400"
        >
            <h2 class="dark:text-white">Why Join the Early Access Program?</h2>
            <p>
                Up to now, NativePHP has focused on Windows, Mac, and Linux. But
                we believe that breaking the mobile frontier is what makes the
                project truly compelling... and truly cross-platform.
            </p>
            <p>
                With
                <strong class="dark:text-white">significant progress</strong>
                already made towards enabling
                <strong class="dark:text-white">NativePHP for mobile</strong>
                , we are excited about the possibilities that lie ahead.
            </p>
            <p>
                However, to make this vision a reality for both iOS and Android,
                we need your support.
            </p>
            <p>
                As an EAP member, you will be supporting the continued
                development of all of NativePHP, but especially of NativePHP for
                mobile.
            </p>
            <p>
                You'll have the opportunity to influence the direction of the
                project and provide critical feedback right from an early stage.
            </p>
            <p>
                You'll get exclusive access to all the latest features first and
                special treatment for the life of the NativePHP project, a
                project we plan to be working on for a long time to come!
            </p>
            <p>
                Please join us on this exciting journey to expand NativePHP onto
                mobile platforms.
            </p>
            <p class="italic">We can't wait to see what you build!</p>
            <p>
                <span class="font-bold italic dark:text-white">
                    Simon &amp; Marcel
                </span>
                <br />
                <span class="text-[#636EC9]">Creators of NativePHP</span>
            </p>
        </div>
    </section>
</x-layout>
