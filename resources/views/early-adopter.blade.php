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
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed"
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
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#EBECF6] p-8 text-center ring-4 ring-inset ring-white/60"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Available on</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">iOS</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm">
                        Join the Early Access Program to start developing iOS
                        apps.
                    </h4>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/ios_phone_mockup.webp') }}"
                            alt=""
                            class="-mb-[10.5rem] w-40"
                        />
                    </div>
                    {{-- White blurred circle --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl"
                    ></div>
                    {{-- Blue blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#9CA8D9]/40 blur-2xl"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#DDE2F3] blur-[100px]"
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
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#F6F1EB] p-8 text-center ring-4 ring-inset ring-white/60"
                >
                    {{-- Subtitle --}}
                    <h6 class="text-sm text-gray-500">Coming soon for</h6>
                    {{-- Title --}}
                    <h2 class="pt-1 text-4xl font-semibold">Android</h2>
                    {{-- Text --}}
                    <h4 class="pt-2.5 text-sm">
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
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl"
                    ></div>
                    {{-- Center blurred circle --}}
                    <div
                        class="absolute bottom-0 right-1/2 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#E0D7CE] blur-2xl"
                    ></div>
                </div>

                {{-- Blurred circle --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#FBF2E7] blur-[100px]"
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
                                    duration: 0.9,
                                    ease: motion.backOut,
                                    at: '-0.5',
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
                                    duration: 0.9,
                                    ease: motion.backOut,
                                    at: '-0.5',
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
                                    duration: 0.9,
                                    ease: motion.backOut,
                                    at: '-0.5',
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
                    class="mt-3 grid h-52 w-72 place-items-center rounded-xl bg-gray-900 p-5 opacity-0"
                >
                    <div class="flex flex-col items-center gap-5 text-center">
                        {{-- Checkmark --}}
                        <div
                            x-ref="checkmark"
                            class="grid size-7 place-items-center rounded-full bg-emerald-400 text-black opacity-0 ring-[9px] ring-emerald-400/30"
                        >
                            <x-icons.checkmark class="size-6" />
                        </div>
                        {{-- Success message --}}
                        <div class="space-y-1 text-white">
                            <div
                                x-ref="success_title"
                                class="font-mediumopacity-0 text-sm"
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
                        class="-mb-12 -mr-12 h-52 w-72 overflow-hidden rounded-xl bg-white opacity-0"
                    >
                        <div
                            class="flex items-center gap-1 bg-gray-100 px-3 py-3.5"
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
    <section class="mx-auto mt-24 max-w-5xl px-5">
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
                class="rounded-2xl bg-gray-100 p-7 opacity-0"
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
                        <span class="font-medium text-black">$100</span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">1</span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">1</span>
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
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Business hours support (GMT)</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
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
                class="rounded-2xl bg-gray-100 p-7 opacity-0"
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
                        <span class="font-medium text-black">$1000</span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
                                Unlimited
                            </span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">10</span>
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
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
                        </div>
                        <div>Business hours support (GMT)</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-zinc-200"
                        >
                            <x-icons.xmark class="size-2.5 shrink-0" />
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
                class="relative rounded-2xl bg-gray-100 p-7 opacity-0 ring-1 ring-black"
            >
                {{-- Popular badge --}}
                <div
                    class="absolute -right-3 -top-5 rounded-xl bg-gradient-to-tr from-[#6886FF] to-[#B8C1FF] px-5 py-2 text-sm text-white"
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
                        <span class="font-medium text-black">$2500</span>
                        after EAP ends.
                    </h6>
                </div>

                {{-- Button --}}
                <a
                    href="#"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900"
                >
                    Get started
                </a>

                {{-- Features --}}
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-2">
                        <x-icons.desktop-computer class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
                                Unlimited
                            </span>
                            app builds
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.upload-box class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
                                Unlimited
                            </span>
                            store releases
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-icons.user-single class="size-5 shrink-0" />
                        <div class="text-zinc-500">
                            <span class="font-medium text-black">
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
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Community Discord channel</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Repo access</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">Vote for mobile features</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
                        >
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <div class="font-medium">
                            Business hours support (GMT)
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div
                            class="grid size-7 place-items-center rounded-xl bg-[#D4FD7D]"
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
            class="prose mx-auto max-w-2xl opacity-0"
        >
            <h2 class="">Why Join the Early Access Program?</h2>
            <p>
                Up to now, NativePHP has focused on Windows, Mac, and Linux. But
                we believe that breaking the mobile frontier is what makes the
                project truly compelling... and truly cross-platform.
            </p>
            <p>
                With
                <strong>significant progress</strong>
                already made towards enabling
                <strong>NativePHP for mobile</strong>
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
                <span class="font-bold italic">Simon &amp; Marcel</span>
                <br />
                <span class="text-[#636EC9]">Creators of NativePHP</span>
            </p>
        </div>
    </section>
</x-layout>
