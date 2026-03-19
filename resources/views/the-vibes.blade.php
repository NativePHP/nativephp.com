<x-layout title="The Vibes - Laracon Day 3">
    <div class="mx-auto max-w-5xl">
        {{-- Hero Section --}}
        <section class="mt-12">
            <div class="grid place-items-center text-center">
                {{-- Badge --}}
                <div
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
                    class="inline-flex items-center gap-2 rounded-full bg-violet-100 px-4 py-1.5 text-sm font-medium text-violet-700 dark:bg-violet-500/15 dark:text-violet-300"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                    July 30, 2026 &middot; Boston, MA
                </div>

                {{-- Title --}}
                <h1
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
                    class="mt-5 text-4xl font-extrabold md:text-5xl lg:text-6xl"
                >
                    The
                    <span class="bg-gradient-to-r from-violet-500 to-indigo-500 bg-clip-text text-transparent dark:from-violet-400 dark:to-indigo-400">Vibes</span>
                </h1>

                {{-- Subtitle --}}
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
                    class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-zinc-400"
                >
                    Don't let the energy end after Laracon. Join us for Day 3 &mdash;
                    an intimate, community-powered gathering to keep the momentum going.
                </p>

                {{-- CTA --}}
                <div
                    x-init="
                        () => {
                            motion.inView($el, (element) => {
                                motion.animate(
                                    $el,
                                    {
                                        y: [10, 0],
                                        opacity: [0, 1],
                                    },
                                    {
                                        duration: 0.7,
                                        ease: motion.backOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mt-8 flex flex-col items-center gap-3"
                >
                    <a
                        href="https://luma.com/szs6n4ym"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-8 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-violet-500/80 dark:drop-shadow-xl dark:drop-shadow-transparent dark:hover:bg-violet-500 dark:hover:drop-shadow-violet-500/30"
                    >
                        Grab Your Spot &mdash; $89
                    </a>
                    <span class="text-sm text-gray-500 dark:text-gray-500">Only 100 spots available</span>
                </div>
            </div>
        </section>

        {{-- Early Bird Countdown --}}
        <section
            class="mt-10"
            x-data="{
                deadline: new Date('2026-04-01T00:00:00').getTime(),
                days: 0,
                hours: 0,
                minutes: 0,
                seconds: 0,
                expired: false,
                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                },
                tick() {
                    const now = Date.now();
                    const diff = this.deadline - now;
                    if (diff <= 0) {
                        this.expired = true;
                        return;
                    }
                    this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    this.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    this.seconds = Math.floor((diff % (1000 * 60)) / 1000);
                },
            }"
        >
            <div
                x-show="!expired"
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [15, 0],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                },
                            )
                        })
                    }
                "
                class="overflow-hidden rounded-2xl bg-gradient-to-r from-violet-600 to-indigo-400 p-6 text-white shadow-lg shadow-violet-500/20 dark:from-violet-400/90 dark:to-indigo-400/90 sm:p-8"
            >
                <div class="flex flex-col items-center gap-6 lg:flex-row lg:justify-between">
                    {{-- Price Info --}}
                    <div class="text-center lg:text-left">
                        <p class="text-sm font-medium uppercase tracking-wider text-yellow-200">
                            Early Bird Pricing
                        </p>
                        <div class="mt-2 flex items-baseline gap-3">
                            <span class="text-4xl font-extrabold">$89</span>
                            <span class="text-lg text-violet-200 line-through">$129</span>
                        </div>
                        <p class="mt-1 text-sm text-violet-200">
                            Price increases April 1st
                        </p>
                    </div>

                    {{-- Countdown --}}
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="grid place-items-center">
                            <span class="text-3xl font-bold tabular-nums sm:text-4xl" x-text="String(days).padStart(2, '0')">00</span>
                            <span class="mt-1 text-xs font-medium uppercase tracking-wide text-violet-200">Days</span>
                        </div>
                        <span class="text-2xl font-bold text-violet-300">:</span>
                        <div class="grid place-items-center">
                            <span class="text-3xl font-bold tabular-nums sm:text-4xl" x-text="String(hours).padStart(2, '0')">00</span>
                            <span class="mt-1 text-xs font-medium uppercase tracking-wide text-violet-200">Hours</span>
                        </div>
                        <span class="text-2xl font-bold text-violet-300">:</span>
                        <div class="grid place-items-center">
                            <span class="text-3xl font-bold tabular-nums sm:text-4xl" x-text="String(minutes).padStart(2, '0')">00</span>
                            <span class="mt-1 text-xs font-medium uppercase tracking-wide text-violet-200">Mins</span>
                        </div>
                        <span class="text-2xl font-bold text-violet-300">:</span>
                        <div class="grid place-items-center">
                            <span class="text-3xl font-bold tabular-nums sm:text-4xl" x-text="String(seconds).padStart(2, '0')">00</span>
                            <span class="mt-1 text-xs font-medium uppercase tracking-wide text-violet-200">Secs</span>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <a
                        href="https://luma.com/szs6n4ym"
                        class="shrink-0 rounded-xl bg-white px-6 py-3 font-semibold text-violet-700 transition duration-200 hover:bg-violet-50"
                    >
                        Lock In $89
                    </a>
                </div>
            </div>
        </section>

        {{-- Hero Image --}}
        <section class="mt-16">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [20, 0],
                                },
                                {
                                    duration: 0.9,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative isolate overflow-hidden rounded-2xl"
            >
                <img
                    src="{{ Vite::asset('resources/images/the-vibes/hero-event.webp') }}"
                    alt="People at a lively social event"
                    loading="eager"
                    class="h-64 w-full object-cover sm:h-80 md:h-96"
                />
            </div>
        </section>

        <x-divider />

        {{-- What Is It Section --}}
        <section class="mt-16">
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
                class="overflow-hidden rounded-2xl bg-gray-100 dark:bg-[#1a1a2e]"
            >
                <div class="grid md:grid-cols-2">
                    {{-- Text --}}
                    <div class="p-8 md:p-12">
                        <h2 class="text-3xl font-semibold">
                            What is The Vibes?
                        </h2>

                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            Laracon US is two days of incredible talks, new connections, and pure
                            excitement for building with Laravel. <strong>But when it's over, you're left
                            buzzing with ideas and wanting more.</strong>
                        </p>

                        <p class="mt-4 text-gray-600 dark:text-gray-400">
                            The Vibes is a curated, single-day gathering on the day after Laracon &mdash;
                            a place to decompress, collaborate, and ride that wave of energy just a little
                            longer. Think local Boston catering, plenty of Coke Zero's, good company,
                            and the kind of conversations that happen when you put 100 passionate
                            developers in a room together.
                        </p>
                    </div>

                    {{-- Image --}}
                    <div class="relative min-h-48 md:min-h-0">
                        <img
                            src="{{ Vite::asset('resources/images/the-vibes/what-is-vibes.webp') }}"
                            alt="The Vibes event atmosphere"
                            loading="lazy"
                            class="h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-violet-500/5 mix-blend-multiply dark:bg-violet-500/10"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- What's Included Section --}}
        <section class="mt-20">
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
                class="text-center text-3xl font-semibold"
            >
                What's Included
            </h2>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [10, 0],
                                    opacity: [0, 1],
                                    scale: [0.8, 1],
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
                class="mt-10 grid gap-x-8 gap-y-12 md:grid-cols-2 lg:grid-cols-3"
            >
                {{-- Catering --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75-1.5.75a3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0 3.354 3.354 0 0 0-3 0 3.354 3.354 0 0 1-3 0L3 16.5m15-3.379a48.474 48.474 0 0 0-6-.371c-2.032 0-4.034.126-6 .371m12 0c.39.049.777.102 1.163.16 1.07.16 1.837 1.094 1.837 2.175v5.169c0 .621-.504 1.125-1.125 1.125H4.125A1.125 1.125 0 0 1 3 20.625v-5.17c0-1.08.768-2.014 1.837-2.174A47.78 47.78 0 0 1 6 13.12" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Catered Food & Drinks</x-slot>
                    <x-slot name="description">
                        Local Boston food, snacks, and drinks all day long. Yes, there will be plenty of Coke Zero's.
                    </x-slot>
                </x-benefit-card>

                {{-- Networking --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Community Networking</x-slot>
                    <x-slot name="description">
                        An intimate setting with 100 like-minded developers. Real conversations, not small talk.
                    </x-slot>
                </x-benefit-card>

                {{-- Atmosphere --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">The Atmosphere</x-slot>
                    <x-slot name="description">
                        A curated space designed to keep the Laracon energy alive. Relaxed, creative, and fun.
                    </x-slot>
                </x-benefit-card>

                {{-- Sponsors --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Sponsor Surprises</x-slot>
                    <x-slot name="description">
                        Our sponsors will be bringing some extras to the table. Stay tuned for announcements.
                    </x-slot>
                </x-benefit-card>

                {{-- Collaboration --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Open Collaboration</x-slot>
                    <x-slot name="description">
                        Space to hack on ideas, pair program, or just geek out about what you learned at Laracon.
                    </x-slot>
                </x-benefit-card>

                {{-- Exclusive --}}
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Limited & Exclusive</x-slot>
                    <x-slot name="description">
                        Only 100 spots. This isn't a massive conference &mdash; it's a handpicked gathering of the community.
                    </x-slot>
                </x-benefit-card>
            </div>
        </section>

        {{-- Photo Break --}}
        <section class="mt-20">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    scale: [0.97, 1],
                                },
                                {
                                    duration: 0.9,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="relative isolate overflow-hidden rounded-2xl"
            >
                <img
                    src="{{ Vite::asset('resources/images/the-vibes/crowd-event.webp') }}"
                    alt="Crowd gathered at a SoWa Power Station event"
                    loading="lazy"
                    class="h-56 w-full object-cover sm:h-72"
                />
                <div class="absolute inset-0 bg-gradient-to-r from-violet-600/20 to-indigo-600/20 mix-blend-multiply dark:from-violet-600/30 dark:to-indigo-600/30"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="text-center text-2xl font-bold text-white drop-shadow-lg sm:text-3xl md:text-4xl">
                        100 devs. One room. All vibes.
                    </p>
                </div>
            </div>
        </section>

        {{-- Venue Gallery Section --}}
        <section
            class="mt-20"
            x-data="{
                open: false,
                current: 0,
                images: [
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Small-Atrium-Lounge-scaled.jpeg', alt: 'Atrium lounge area' },
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Elisif_20230621_3502-websize-scaled.jpg', alt: 'Venue interior' },
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Elisif_20230621_3355-HDR-websize-scaled.jpg', alt: 'Venue space' },
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Assembly-Best-photo.jpeg', alt: 'Assembly hall' },
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Library-SMALL-scaled.jpg', alt: 'Library space' },
                    { src: 'https://www.uniquevenues.com/wp-content/uploads/2023/11/Library-Atrium-SMALL--scaled.jpg', alt: 'Library atrium' },
                ],
                openLightbox(index) {
                    this.current = index;
                    this.open = true;
                },
                next() {
                    this.current = (this.current + 1) % this.images.length;
                },
                prev() {
                    this.current = (this.current - 1 + this.images.length) % this.images.length;
                },
            }"
            @keydown.escape.window="open = false"
            @keydown.arrow-right.window="if (open) next()"
            @keydown.arrow-left.window="if (open) prev()"
        >
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
                class="text-center text-3xl font-semibold"
            >
                The Venue
            </h2>

            {{-- Featured Image --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    y: [20, 0],
                                },
                                {
                                    duration: 0.9,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mt-10 cursor-pointer overflow-hidden rounded-2xl"
                @click="openLightbox(0)"
            >
                <img
                    src="https://www.uniquevenues.com/wp-content/uploads/2023/11/Small-Atrium-Lounge-scaled.jpeg"
                    alt="Atrium lounge area"
                    loading="lazy"
                    class="h-64 w-full object-cover transition duration-300 hover:scale-105 sm:h-80 md:h-[28rem]"
                />
            </div>

            {{-- Thumbnail Grid --}}
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [10, 0],
                                    opacity: [0, 1],
                                    scale: [0.95, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.08),
                                },
                            )
                        })
                    }
                "
                class="mt-4 grid grid-cols-5 gap-3"
            >
                <template x-for="(image, index) in images.slice(1)" :key="index">
                    <div
                        class="cursor-pointer overflow-hidden rounded-xl"
                        @click="openLightbox(index + 1)"
                    >
                        <img
                            :src="image.src"
                            :alt="image.alt"
                            loading="lazy"
                            class="h-20 w-full object-cover transition duration-300 hover:scale-110 sm:h-24 md:h-32"
                        />
                    </div>
                </template>
            </div>

            {{-- Lightbox Overlay --}}
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-transition:enter="transition duration-300 ease-out"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition duration-200 ease-in"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4 backdrop-blur-sm"
                    @click.self="open = false"
                >
                    {{-- Close Button --}}
                    <button
                        @click="open = false"
                        class="absolute right-4 top-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>

                    {{-- Previous Button --}}
                    <button
                        @click="prev()"
                        class="absolute left-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    </button>

                    {{-- Image --}}
                    <img
                        :src="images[current].src"
                        :alt="images[current].alt"
                        class="max-h-[85vh] max-w-full rounded-lg object-contain"
                    />

                    {{-- Next Button --}}
                    <button
                        @click="next()"
                        class="absolute right-4 rounded-full bg-white/10 p-2 text-white transition hover:bg-white/20"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>

                    {{-- Image Counter --}}
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-4 py-1.5 text-sm text-white">
                        <span x-text="current + 1"></span> / <span x-text="images.length"></span>
                    </div>
                </div>
            </template>
        </section>

        <x-divider />

        {{-- Event Details Section --}}
        <section id="details" class="mt-16 scroll-mt-32">
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
                class="text-center text-3xl font-semibold"
            >
                Event Details
            </h2>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
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
                class="mt-10 grid gap-6 md:grid-cols-2"
            >
                {{-- Date & Time --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e]">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 dark:bg-violet-500/15">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium">Date & Time</h3>
                    </div>
                    <p class="mt-4 text-lg font-semibold">
                        Thursday, July 30, 2026
                    </p>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        9:00 AM &ndash; 4:00 PM
                    </p>
                </div>

                {{-- Location --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e]">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 dark:bg-violet-500/15">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium">Location</h3>
                    </div>
                    <p class="mt-4 text-lg font-semibold">
                        Loft on Two
                    </p>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        One Financial Center, Boston, MA 02111
                    </p>
                </div>

                {{-- Price --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e]">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 dark:bg-violet-500/15">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium">Ticket Price</h3>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <p class="text-lg font-semibold">$89 per person</p>
                        <span class="text-sm text-gray-500 line-through dark:text-gray-500">$129</span>
                    </div>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Early bird pricing until April 1st. Includes catering, drinks, and full event access.
                    </p>
                </div>

                {{-- Capacity --}}
                <div class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e]">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-violet-100 dark:bg-violet-500/15">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-violet-600 dark:text-violet-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-medium">Capacity</h3>
                    </div>
                    <p class="mt-4 text-lg font-semibold">
                        100 attendees
                    </p>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        Limited to keep it personal. Once they're gone, they're gone.
                    </p>
                </div>
            </div>
        </section>

        {{-- Sponsors Section --}}
        <section class="mt-20">
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
                class="text-center text-3xl font-semibold"
            >
                Sponsored By
            </h2>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
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
                class="mt-10 grid grid-cols-2 gap-5 md:grid-cols-4"
            >
                {{-- Web Mavens --}}
                <a
                    href="https://www.webmavens.com/?ref=nativephp"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="grid h-28 place-items-center rounded-2xl bg-gray-100 px-6 transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-gray-200/80 hover:shadow-lg hover:shadow-gray-200/70 dark:bg-[#1a1a2e] dark:hover:bg-slate-800/80 dark:hover:shadow-transparent"
                >
                    <div class="grid h-15 w-35 place-items-center">
                        <x-sponsors.logos.webmavens
                            class="dark:fill-white"
                            aria-hidden="true"
                        />
                    </div>
                    <span class="sr-only">Web Mavens</span>
                </a>

                {{-- Nexcalia --}}
                <a
                    href="https://www.nexcalia.com/?ref=nativephp"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="grid h-28 place-items-center rounded-2xl bg-gray-100 px-6 transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-gray-200/80 hover:shadow-lg hover:shadow-gray-200/70 dark:bg-[#1a1a2e] dark:hover:bg-slate-800/80 dark:hover:shadow-transparent"
                >
                    <div class="grid h-15 w-35 place-items-center">
                        <x-sponsors.logos.nexcalia
                            class="text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </div>
                    <span class="sr-only">Nexcalia</span>
                </a>

                {{-- Bifrost Technology --}}
                <a
                    href="https://bifrost.nativephp.com/"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="grid h-28 place-items-center rounded-2xl bg-gray-100 px-6 transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-gray-200/80 hover:shadow-lg hover:shadow-gray-200/70 dark:bg-[#1a1a2e] dark:hover:bg-slate-800/80 dark:hover:shadow-transparent"
                >
                    <div class="grid h-15 w-35 place-items-center">
                        <x-logos.bifrost class="h-6" />
                    </div>
                    <span class="sr-only">Bifrost Technology</span>
                </a>

                {{-- Beyond Code --}}
                <a
                    href="https://beyondco.de/?utm_source=nativephp&utm_medium=logo&utm_campaign=the-vibes"
                    target="_blank"
                    rel="noopener noreferrer sponsored"
                    class="grid h-28 place-items-center rounded-2xl bg-gray-100 px-6 transition duration-200 will-change-transform hover:-translate-y-0.5 hover:bg-gray-200/80 hover:shadow-lg hover:shadow-gray-200/70 dark:bg-[#1a1a2e] dark:hover:bg-slate-800/80 dark:hover:shadow-transparent"
                >
                    <div class="grid h-15 w-35 place-items-center">
                        <img
                            src="/img/sponsors/beyondcode.webp"
                            class="block dark:hidden"
                            loading="lazy"
                            alt="BeyondCode logo"
                            width="160"
                            height="40"
                        />
                        <img
                            src="/img/sponsors/beyondcode-dark.webp"
                            class="hidden dark:block"
                            loading="lazy"
                            alt="BeyondCode logo"
                            width="160"
                            height="40"
                        />
                    </div>
                    <span class="sr-only">Beyond Code</span>
                </a>
            </div>

            {{-- Become a Sponsor CTA --}}
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
                class="mt-8 text-center"
            >
                <p class="text-gray-600 dark:text-gray-400">
                    Interested in sponsoring The Vibes?
                </p>
                <a
                    href="mailto:support@nativephp.com?subject=The%20Vibes%20-%20Sponsorship%20Inquiry"
                    class="mt-3 inline-flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-6 py-3 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                    Get in Touch
                </a>
            </div>
        </section>

        {{-- Bottom CTA --}}
        <section class="mt-16 pb-24">
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
                class="grid place-items-center text-center"
            >
                <h2 class="text-3xl font-bold md:text-4xl">
                    Don't Let the
                    <span class="bg-gradient-to-r from-violet-500 to-indigo-500 bg-clip-text text-transparent dark:from-violet-400 dark:to-indigo-400">Vibes</span>
                    End
                </h2>

                <p class="mx-auto mt-4 max-w-xl text-gray-600 dark:text-gray-400">
                    Laracon gives you the spark. The Vibes keeps it lit. Grab your spot before they sell out.
                </p>

                <div class="mt-8 flex flex-col items-center gap-3">
                    <a
                        href="https://luma.com/szs6n4ym"
                        class="flex items-center justify-center gap-2.5 rounded-xl bg-zinc-800 px-8 py-4 text-white transition duration-200 hover:bg-zinc-900 dark:bg-violet-500/80 dark:drop-shadow-xl dark:drop-shadow-transparent dark:hover:bg-violet-500 dark:hover:drop-shadow-violet-500/30"
                    >
                        Get Your Ticket &mdash; $89
                    </a>
                    <span class="text-sm text-gray-500 dark:text-gray-500">
                        Early bird $89 &middot; $129 after April 1st &middot; 100 spots
                    </span>
                </div>
            </div>
        </section>
    </div>
</x-layout>
