<div
    x-init="
        () => {
            motion.inView($el, (element) => {
                gsap.fromTo(
                    $el,
                    { scale: 0.95, autoAlpha: 0 },
                    {
                        scale: 1,
                        autoAlpha: 1,
                        duration: 0.8,
                        ease: 'back.out(1.2)',
                    },
                )
            })
        }
    "
    class="relative z-0 h-full overflow-hidden rounded-2xl from-violet-600 via-fuchsia-500 to-orange-400 p-1 ring-1 ring-zinc-200/50 dark:ring-white/20"
    aria-labelledby="plugins-title"
    role="region"
>
        {{-- Inner container --}}
        <div
            class="relative z-0 flex h-full flex-col items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-[#F9F9F9] via-white to-[#F9F9F9] px-6 py-12 text-center md:px-12 md:py-16 lg:py-20 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950"
        >
            {{-- Animated background grid --}}
            <div
                class="pointer-events-none absolute inset-0 -z-10 opacity-20 dark:opacity-20"
                aria-hidden="true"
            >
                <div
                    class="absolute inset-0"
                    style="background-image: linear-gradient(rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.05) 1px, transparent 1px); background-size: 40px 40px;"
                ></div>
                <div
                    class="absolute inset-0 hidden dark:block"
                    style="background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px;"
                ></div>
            </div>

            {{-- Glowing orbs --}}
            <div
                x-init="
                    () => {
                        gsap.to($el, {
                            x: 50,
                            y: -30,
                            duration: 8,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut',
                        })
                    }
                "
                class="pointer-events-none absolute -top-20 -left-20 -z-5 size-60 rounded-full bg-violet-500/30 blur-[80px]"
                aria-hidden="true"
            ></div>
            <div
                x-init="
                    () => {
                        gsap.to($el, {
                            x: -40,
                            y: 20,
                            duration: 6,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut',
                        })
                    }
                "
                class="pointer-events-none absolute -right-20 -bottom-20 -z-5 size-60 rounded-full bg-fuchsia-500/30 blur-[80px]"
                aria-hidden="true"
            ></div>
            <div
                x-init="
                    () => {
                        gsap.to($el, {
                            scale: 1.2,
                            duration: 4,
                            repeat: -1,
                            yoyo: true,
                            ease: 'sine.inOut',
                        })
                    }
                "
                class="pointer-events-none absolute top-1/2 left-1/2 -z-5 size-40 -translate-x-1/2 -translate-y-1/2 rounded-full bg-orange-500/20 blur-[60px]"
                aria-hidden="true"
            ></div>

            {{-- NEW badge --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                { scale: 0, rotate: -10 },
                                {
                                    scale: 1,
                                    rotate: 0,
                                    duration: 0.6,
                                    delay: 0.3,
                                    ease: 'back.out(2)',
                                },
                            )
                        })
                    }
                "
                class="mb-4 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-violet-500 to-fuchsia-500 px-4 py-1.5 text-sm font-semibold text-white shadow-lg shadow-violet-500/25"
            >
                <span
                    x-init="
                        () => {
                            gsap.to($el, {
                                scale: 1.2,
                                duration: 0.5,
                                repeat: -1,
                                yoyo: true,
                                ease: 'power1.inOut',
                            })
                        }
                    "
                    class="inline-block"
                >
                    <x-icons.plug class="size-4" />
                </span>
                NEW
            </div>

            {{-- Main headline --}}
            <h2
                id="plugins-title"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                { y: 30, autoAlpha: 0 },
                                {
                                    y: 0,
                                    autoAlpha: 1,
                                    duration: 0.8,
                                    delay: 0.2,
                                    ease: 'power3.out',
                                },
                            )
                        })
                    }
                "
                class="text-2xl font-semibold tracking-tight text-gray-800 xs:text-3xl sm:text-4xl dark:text-white"
            >
                <span class="block">Build anything with</span>
                <span
                    class="block bg-gradient-to-r from-violet-400 via-fuchsia-400 to-orange-400 bg-clip-text pb-1 text-transparent"
                >
                    Plugins
                </span>
            </h2>

            {{-- Subtitle --}}
            <p
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                { y: 20, autoAlpha: 0 },
                                {
                                    y: 0,
                                    autoAlpha: 1,
                                    duration: 0.8,
                                    delay: 0.4,
                                    ease: 'power2.out',
                                },
                            )
                        })
                    }
                "
                class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 sm:text-xl md:mt-6 md:text-2xl dark:text-slate-300"
            >
                Extend your mobile apps with powerful plugins.
                <span class="text-gray-900 dark:text-white">Unlimited possibilities.</span>
            </p>

            {{-- CTA Button --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                $el,
                                { y: 20, autoAlpha: 0 },
                                {
                                    y: 0,
                                    autoAlpha: 1,
                                    duration: 0.6,
                                    delay: 0.7,
                                    ease: 'power2.out',
                                },
                            )
                        })
                    }
                "
                class="mt-8 md:mt-10"
            >
                <a
                    href="/plugins"
                    class="group inline-flex items-center gap-2 rounded-full bg-gray-900 px-4 py-2 text-sm font-medium text-white transition duration-300 hover:bg-gray-800 dark:bg-white dark:text-slate-900 dark:hover:bg-gray-100"
                >
                    <span>Explore Plugins</span>
                    <svg
                        class="size-4 transition duration-300 group-hover:translate-x-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>

            {{-- Decorative corner elements --}}
            <div class="pointer-events-none absolute top-4 left-4 size-8 border-l-2 border-t-2 border-gray-300 md:top-6 md:left-6 md:size-12 dark:border-white/20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute top-4 right-4 size-8 border-r-2 border-t-2 border-gray-300 md:top-6 md:right-6 md:size-12 dark:border-white/20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute bottom-4 left-4 size-8 border-b-2 border-l-2 border-gray-300 md:bottom-6 md:left-6 md:size-12 dark:border-white/20" aria-hidden="true"></div>
            <div class="pointer-events-none absolute right-4 bottom-4 size-8 border-r-2 border-b-2 border-gray-300 md:right-6 md:bottom-6 md:size-12 dark:border-white/20" aria-hidden="true"></div>
        </div>
    </div>
