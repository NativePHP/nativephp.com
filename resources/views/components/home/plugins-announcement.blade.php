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
    class="relative z-0 h-full overflow-hidden rounded-2xl  from-violet-600 via-fuchsia-500 to-orange-400 p-1 ring-1 ring-white/20"
    aria-labelledby="plugins-title"
    role="region"
>
        {{-- Inner container --}}
        <div
            class="relative z-0 flex h-full flex-col items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 px-6 py-12 text-center md:px-12 md:py-16 lg:py-20"
        >
            {{-- Animated background grid --}}
            <div
                class="pointer-events-none absolute inset-0 -z-10 opacity-20"
                aria-hidden="true"
            >
                <div
                    class="absolute inset-0"
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
                    <svg class="size-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                    </svg>
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
                class="text-4xl font-black uppercase tracking-tight text-white xs:text-5xl sm:text-6xl "
            >
                <span class="block">Mobile Plugins</span>
                <span
                    class="block bg-gradient-to-r from-violet-400 via-fuchsia-400 to-orange-400 bg-clip-text text-transparent"
                >
                    Are Here
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
                class="mx-auto mt-4 max-w-2xl text-lg text-slate-300 sm:text-xl md:mt-6 md:text-2xl"
            >
                Extend your mobile apps with powerful, community-driven plugins.
                <span class="text-white">Unlimited possibilities.</span>
            </p>

            {{-- Feature pills --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            gsap.fromTo(
                                Array.from($el.children),
                                { y: 20, autoAlpha: 0 },
                                {
                                    y: 0,
                                    autoAlpha: 1,
                                    stagger: 0.1,
                                    duration: 0.5,
                                    delay: 0.5,
                                    ease: 'power2.out',
                                },
                            )
                        })
                    }
                "
                class="mt-6 flex flex-wrap justify-center gap-2 md:mt-8 md:gap-3"
            >
                <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm ring-1 ring-white/20">
                    Easy to install
                </span>
{{--                <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm ring-1 ring-white/20">--}}
{{--                    Community built--}}
{{--                </span>--}}
                <span class="rounded-full bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-sm ring-1 ring-white/20">
                    Fully documented
                </span>

            </div>

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
                    class="group relative inline-flex items-center gap-3 overflow-hidden rounded-full bg-white px-8 py-4 text-lg font-bold text-slate-900 shadow-2xl shadow-white/20 transition duration-300 hover:scale-105 hover:shadow-white/30"
                >
                    <span>Explore Plugins</span>
                    <svg
                        class="size-5 transition duration-300 group-hover:translate-x-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
                    {{-- Shimmer effect --}}
                    <div
                        x-init="
                            () => {
                                gsap.fromTo(
                                    $el,
                                    { x: '-100%' },
                                    {
                                        x: '200%',
                                        duration: 2,
                                        repeat: -1,
                                        repeatDelay: 3,
                                        ease: 'power2.inOut',
                                    },
                                )
                            }
                        "
                        class="absolute inset-y-0 -left-full w-1/2 bg-gradient-to-r from-transparent via-violet-500/20 to-transparent skew-x-12"
                        aria-hidden="true"
                    ></div>
                </a>
            </div>

            {{-- Decorative corner elements --}}
            <div class="pointer-events-none absolute top-4 left-4 size-8 border-l-2 border-t-2 border-white/20 md:top-6 md:left-6 md:size-12" aria-hidden="true"></div>
            <div class="pointer-events-none absolute top-4 right-4 size-8 border-r-2 border-t-2 border-white/20 md:top-6 md:right-6 md:size-12" aria-hidden="true"></div>
            <div class="pointer-events-none absolute bottom-4 left-4 size-8 border-b-2 border-l-2 border-white/20 md:bottom-6 md:left-6 md:size-12" aria-hidden="true"></div>
            <div class="pointer-events-none absolute right-4 bottom-4 size-8 border-r-2 border-b-2 border-white/20 md:right-6 md:bottom-6 md:size-12" aria-hidden="true"></div>
        </div>
    </div>
