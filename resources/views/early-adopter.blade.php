<x-layout title="NativePHP for iOS and Android">
    {{-- Hero Section --}}
    <section
        class="mt-10 px-5 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Primary Heading --}}
            <h1
                id="hero-heading"
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
                NativePHP for Mobile
            </h1>

            {{-- Introduction Description --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-4 text-base/relaxed text-gray-600 sm:text-lg/relaxed dark:text-gray-400"
            >
                This changes everything! Now you can use the tools you already
                know to build rich, native,
                <em>mobile</em>
                apps... right now!
            </h2>
        </header>

        {{-- Platform Cards --}}
        <div
            class="flex flex-wrap items-center justify-center gap-6 pt-10"
            aria-label="Available platforms"
        >
            {{-- iOS Card --}}
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
                aria-labelledby="ios-heading"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#EBECF6] p-8 text-center ring-4 ring-white/60 ring-inset dark:bg-black/50 dark:ring-white/5"
                >
                    {{-- Subtitle --}}
                    <p class="text-sm text-gray-500">Available now for</p>
                    {{-- Title --}}
                    <h3
                        id="ios-heading"
                        class="pt-1 text-4xl font-semibold"
                    >
                        iOS
                    </h3>
                    {{-- Text --}}
                    <p class="pt-2.5 text-sm dark:text-gray-400">
                        Build and ship App Store approved iOS apps today!
                    </p>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/ios_phone_mockup.webp') }}"
                            alt="iOS phone mockup displaying a NativePHP application"
                            class="-mb-44 w-40 dark:mix-blend-hard-light"
                        />
                    </div>
                    {{-- White blurred circle - Decorative --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl dark:bg-[#3a3f67]"
                        aria-hidden="true"
                    ></div>
                    {{-- Blue blurred circle - Decorative --}}
                    <div
                        class="absolute right-1/2 bottom-0 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#9CA8D9]/40 blur-2xl dark:bg-blue-800/40"
                        aria-hidden="true"
                    ></div>
                </div>

                {{-- Blurred circle - Decorative --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#DDE2F3] blur-[100px] dark:bg-[#444892]/50"
                    aria-hidden="true"
                ></div>
            </div>

            {{-- Android Card --}}
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
                aria-labelledby="android-heading"
            >
                <div
                    class="relative isolate z-10 flex w-full max-w-xs flex-col items-center overflow-hidden rounded-2xl bg-[#F6F1EB] p-8 text-center ring-4 ring-white/60 ring-inset dark:bg-black/50 dark:ring-white/5"
                >
                    {{-- Subtitle --}}
                    <p class="text-sm text-gray-500">Available now for</p>
                    {{-- Title --}}
                    <h3
                        id="android-heading"
                        class="pt-1 text-4xl font-semibold"
                    >
                        Android
                    </h3>
                    {{-- Text --}}
                    <p class="pt-2.5 text-sm dark:text-gray-400">
                        Sign up and build apps for Android today!
                    </p>
                    {{-- Mockup --}}
                    <div class="pt-10">
                        <img
                            src="{{ Vite::asset('resources/images/mobile/android_phone_mockup.webp') }}"
                            alt="Android phone mockup displaying a NativePHP application"
                            class="-mb-[10.7rem] w-40"
                        />
                    </div>
                    {{-- White blurred circle - Decorative --}}
                    <div
                        class="absolute -top-5 right-1/2 -z-10 h-40 w-14 translate-x-1/2 rounded-full bg-white blur-2xl dark:bg-[#3a3f67]"
                        aria-hidden="true"
                    ></div>
                    {{-- Center blurred circle - Decorative --}}
                    <div
                        class="absolute right-1/2 bottom-0 -z-10 h-52 w-72 translate-x-1/2 rounded-full bg-[#E0D7CE] blur-2xl dark:bg-slate-700/50"
                        aria-hidden="true"
                    ></div>
                </div>

                {{-- Blurred circle - Decorative --}}
                <div
                    class="absolute -top-1/2 left-0 -z-20 h-60 w-full rounded-full bg-[#FBF2E7] blur-[100px] dark:bg-slate-500/40"
                    aria-hidden="true"
                ></div>
            </div>
        </div>
    </section>

    {{-- Quick Instructions Section --}}
    <section
        class="mt-20 px-5"
        aria-labelledby="instructions-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Section Heading --}}
            <h2
                id="instructions-heading"
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

            {{-- Section Description --}}
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
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0 dark:text-gray-400"
            >
                Get your app up and running in minutes.
            </p>
        </header>

        {{-- Installation Steps --}}
        <div
            x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate([
                                {{-- Slide 1 --}}
                                [
                                    $refs.slide1.querySelector('h3'),
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
                                    $refs.slide1.querySelector('p'),
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
                                    $refs.slide2.querySelector('h3'),
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                        at: 0.2,
                                    },
                                ],
                                [
                                    $refs.slide2.querySelector('p'),
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
                                    $refs.slide3.querySelector('h3'),
                                    {
                                        opacity: [0, 1],
                                        x: [-10, 0],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.circOut,
                                        at: 0.35,
                                    },
                                ],
                                [
                                    $refs.slide3.querySelector('p'),
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
            aria-label="Installation steps"
        >
            {{-- Step 1 --}}
            <div
                x-ref="slide1"
                aria-labelledby="step1-heading"
            >
                {{-- Step number --}}
                <h3
                    id="step1-heading"
                    class="font-medium opacity-0"
                >
                    Step 1
                </h3>
                {{-- Step description --}}
                <p
                    class="pt-0.5 text-sm text-gray-500 opacity-0 dark:text-gray-400"
                >
                    Buy a license.
                </p>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="dark:bg-mirage mt-3 grid h-52 w-72 place-items-center rounded-xl bg-[#f4f1ee] p-5 opacity-0"
                    aria-label="Purchase confirmation visualization"
                >
                    <div class="flex flex-col items-center gap-5 text-center">
                        {{-- Checkmark --}}
                        <div
                            x-ref="checkmark"
                            class="relative grid size-7 place-items-center rounded-full bg-emerald-400 text-black opacity-0 ring-[9px] ring-emerald-400/20"
                            aria-hidden="true"
                        >
                            <x-icons.checkmark class="size-6" />

                            <div
                                class="absolute top-1/2 right-1/2 hidden size-24 translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-400/20 blur-2xl dark:block"
                                aria-hidden="true"
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

            {{-- Step 2 --}}
            <div
                x-ref="slide2"
                aria-labelledby="step2-heading"
            >
                {{-- Step number --}}
                <h3
                    id="step2-heading"
                    class="font-medium opacity-0"
                >
                    Step 2
                </h3>
                {{-- Step description --}}
                <p
                    class="pt-0.5 text-sm text-gray-500 opacity-0 dark:text-gray-400"
                >
                    Install the private Composer package.
                </p>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="relative isolate z-0 mt-3 grid h-52 w-72 place-items-center overflow-hidden rounded-xl opacity-0"
                    aria-label="Terminal command example"
                >
                    {{-- Terminal --}}
                    <div
                        x-ref="terminal"
                        class="-mr-12 -mb-12 h-52 w-72 overflow-hidden rounded-xl bg-white opacity-0 dark:bg-gray-900"
                    >
                        <div
                            class="flex items-center gap-1 bg-gray-100 px-3 py-3.5 dark:bg-gray-800"
                        >
                            <div
                                class="size-1.5 rounded-full bg-rose-400"
                                aria-hidden="true"
                            ></div>
                            <div
                                class="size-1.5 rounded-full bg-yellow-400"
                                aria-hidden="true"
                            ></div>
                            <div
                                class="size-1.5 rounded-full bg-green-400"
                                aria-hidden="true"
                            ></div>
                        </div>
                        <div class="space-y-1 p-4 text-xs">
                            <div
                                x-ref="bashline1"
                                class="text-gray-500"
                                aria-label="Terminal directory path"
                            >
                                ~/native-php-app
                            </div>
                            <div
                                x-ref="bashline2"
                                class="font-medium"
                                aria-label="Composer install command"
                            >
                                composer require nativephp/mobile
                            </div>
                        </div>
                    </div>
                    {{-- Background image - Decorative --}}
                    <img
                        src="{{ Vite::asset('resources/images/mobile/macos_wallpaper.webp') }}"
                        alt=""
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                        aria-hidden="true"
                    />
                </div>
            </div>

            {{-- Step 3 --}}
            <div
                x-ref="slide3"
                aria-labelledby="step3-heading"
            >
                {{-- Step number --}}
                <h3
                    id="step3-heading"
                    class="font-medium opacity-0"
                >
                    Step 3
                </h3>
                {{-- Step description --}}
                <p
                    class="pt-0.5 text-sm text-gray-500 opacity-0 dark:text-gray-400"
                >
                    Build your app.
                </p>
                {{-- Box --}}
                <div
                    x-ref="box"
                    class="relative isolate z-0 mt-3 grid h-52 w-72 place-items-center overflow-hidden rounded-xl opacity-0"
                    aria-label="Developer using an app built with NativePHP for Mobile"
                >
                    {{-- Background image --}}
                    <img
                        src="{{ Vite::asset('resources/images/mobile/developer_holding_phone.webp') }}"
                        alt="Developer holding a phone with NativePHP application"
                        class="absolute inset-0 -z-10 h-full w-full object-cover"
                        loading="lazy"
                    />
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing Section --}}
    <livewire:mobile-pricing />

    {{-- Ultra Section --}}
    <section
        class="mx-auto mt-16 max-w-4xl px-5"
        aria-labelledby="ultra-tier-heading"
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
            class="dark:bg-mirage space-y-6 rounded-2xl bg-gray-100 p-7"
        >
            {{-- Plan Name --}}
            <h3
                id="ultra-tier-heading"
                class="text-2xl font-semibold"
            >
                Ultra
            </h3>

            {{-- Price --}}
            {{-- <div --}}
            {{-- class="flex items-start gap-1.5 pt-5" --}}
            {{-- aria-label="Price: $10,000+ per year" --}}
            {{-- > --}}
            {{-- <div class="text-5xl font-semibold">$20,000</div> --}}
            {{-- <div class="self-end pb-1.5 text-zinc-500">per year</div> --}}
            {{-- </div> --}}

            <p class="dark:text-gray-400">
                A partnership program offering dedicated support, training,
                license management, early access to build services, and other
                enterprise-oriented services for businesses.
            </p>
            <div>
                <a
                    href="{{ route('partners') }}"
                    class="mt-5 block w-full max-w-xs rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                >
                    Learn More
                </a>
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    {{-- <x-testimonials /> --}}

    {{-- FAQ Section --}}
    <section
        class="mx-auto mt-24 max-w-5xl px-5"
        aria-labelledby="faq-heading"
    >
        {{-- Section Heading --}}
        <h2
            id="faq-heading"
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

        {{-- FAQ List --}}
        <div
            x-init="
                () => {
                    motion.inView($el, (element) => {
                        motion.animate(
                            Array.from($el.children),
                            {
                                x: [-50, 0],
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
            class="mx-auto flex w-full max-w-2xl flex-col items-center gap-4 pt-10"
            aria-labelledby="faq-heading"
        >
            <x-faq-card question="Why isn't this open source and free?">
                <p>
                    We want to build an amazing tool and make it sustainable. A
                    lot of NativePHP is already fully open source. With
                    NativePHP for Mobile, we need to take a different path to
                    ensure that the entire project continues to get the support
                    it deserves.
                </p>
            </x-faq-card>

            <x-faq-card
                question="Will there ever be a free & open source version?"
            >
                <p>
                    Yes! Once we've hit sustainability and can afford to
                    continue investing in this project indirectly, then a
                    version of it will be fully open source and made available
                    for free.
                </p>
            </x-faq-card>

            <x-faq-card
                question="Can I create both iOS and Android apps with one license?"
            >
                <p>
                    Yes, a single license will let you build apps for both iOS
                    and Android.
                </p>
            </x-faq-card>

            <x-faq-card
                question="What does 'build unlimited apps, release X production apps' mean?"
            >
                <p>
                    Every NativePHP for Mobile license entitles you to
                    <i>release</i>
                    a certain number of applications. A 'released' app is when
                    it's published or distributed (typically through an app
                    store).
                </p>
                <br />
                <p>
                    However, you are welcome to
                    <i>build</i>
                    (develop), as many apps as you want. If no one else besides
                    you and other license holders have access to your app, then
                    it doesn't count towards your quota of 'released' apps.
                </p>
            </x-faq-card>

            <x-faq-card question="What is a 'developer seat'?">
                <p>
                    A 'developer seat' represents an individual developer that
                    can build apps under that license plan.
                </p>
                <br />
                <p>
                    For example, on the Pro plan, the license holder can create
                    up to 10 unique license keys that can be assigned to
                    individual developers, one key per developer.
                </p>
                <br />
                <p>
                    This allows the license holder to control who has the
                    ability to use their license for building apps. You can add
                    or remove developers and each will get their own unique
                    license key. If a developer leaves your team, you can revoke
                    their license key to prevent abuse.
                </p>
            </x-faq-card>

            <x-faq-card
                question="Is it possible to purchase additional developer seats or released apps?"
            >
                <p>You can purchase more Mini or Pro licenses as needed.</p>
            </x-faq-card>

            <x-faq-card question="What was the EAP and when did it end?">
                <p>
                    The Early Access Program (EAP) provided initial access to
                    the NativePHP for Mobile platform for early adopters at
                    reduced pricing while we worked on releasing v1 of the
                    technology. The EAP started in February and ended in May
                    2025.
                </p>
            </x-faq-card>

            <x-faq-card question="Which price will my license renew at?">
                <p>
                    Your license will renew at the price you originally paid, as
                    long as you renew before it expires. If you renew after it
                    expires, then you will have to pay the prices available at
                    that time.
                </p>
            </x-faq-card>

            <x-faq-card
                question="Can I still build apps if I choose not to renew my license?"
            >
                <p>
                    Yes. Renewing your license entitles you to receive the
                    latest package updates but isn't required to build and
                    release apps.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I upgrade or downgrade my license later?">
                <p>That's not currently possible.</p>
            </x-faq-card>

            <x-faq-card question="Will my apps built with NativePHP be secure?">
                <p>
                    Definitely. NativePHP for Mobile apps are just like other
                    iOS and Android apps - they're as secure as you make them.
                </p>
            </x-faq-card>
            {{-- <x-faq-card question="Can I try NativePHP before purchasing?"> --}}
            {{-- <p> --}}
            {{-- </p> --}}
            {{-- </x-faq-card> --}}
            <x-faq-card question="Can I use NativePHP for commercial projects?">
                <p>
                    Absolutely! You can use NativePHP for any kind of project,
                    including commercial ones. We can't wait to see what you
                    build!
                </p>
            </x-faq-card>

            <x-faq-card question="Can I get an invoice?">
                <p>
                    If you purchased after May 6, 2025, you should get an
                    invoice with your receipt via email.
                </p>
                <p class="mt-4">
                    For purchases made before this, you simply need to
                    <a
                        href="https://zenvoice.io/p/67a61665e7a3400c73fb75af"
                        onclick="event.stopPropagation()"
                        class="inline-block underline hover:text-violet-400"
                    >
                        follow the instructions here
                    </a>
                    to generate your invoice.
                </p>
            </x-faq-card>

            <x-faq-card question="How can I manage my subscription?">
                <p>
                    You can manage your subscription via the
                    <a
                        href="https://billing.stripe.com/p/login/4gwaGV5VK0uU44E288"
                        onclick="event.stopPropagation()"
                        class="inline-block underline hover:text-violet-400"
                        aria-label="Stripe billing portal"
                        target="_blank"
                    >
                        Stripe billing portal.
                    </a>
                </p>
            </x-faq-card>
        </div>
    </section>
</x-layout>
