<x-layout title="NativePHP for iOS and Android">
    {{-- Hero --}}
    <section class="mt-10 px-5 md:mt-14">
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Tagline --}}
            <h1
                x-init="
                    () => {
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
            {{-- Tagline --}}
            <h2
                x-init="
                    () => {
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
                    }
                "
                class="text-3xl font-semibold"
            >
                Quick instructions
            </h2>

            {{-- Description --}}
            <h3
                x-init="
                    () => {
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
                    }
                "
                class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600"
            >
                It's just as easy you think it'd be
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
                            <svg
                                viewBox="0 0 16 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="size-6"
                            >
                                <path
                                    d="M12.466666666666665 4.800013333333333c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0L6.533333333333333 9.799999999999999l-2.0666666666666664 -2.0666666666666664c-0.26666666666666666 -0.26666666666666666 -0.6666666666666666 -0.26666666666666666 -0.9333333333333332 0 -0.26666666666666666 0.26666666666666666 -0.26666666666666666 0.6666666666666666 0 0.9333333333333332l2.533333333333333 2.533333333333333c0.13333333333333333 0.13333333333333333 0.26666666666666666 0.19999999999999998 0.4666666666666666 0.19999999999999998 0.19999999999999998 0 0.3333333333333333 -0.06666666666666667 0.4666666666666666 -0.19999999999999998l5.466666666666666 -5.466653333333333c0.26666666666666666 -0.26666666666666666 0.26666666666666666 -0.6666666666666666 0 -0.9333333333333332Z"
                                    fill="currentColor"
                                    stroke-width="0.6667"
                                ></path>
                            </svg>
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
</x-layout>
