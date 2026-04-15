<x-layout title="The NativePHP Masterclass">
    <div class="mx-auto max-w-5xl">
        {{-- Hero Section --}}
        <section
            class="mt-12 md:mt-20"
            aria-labelledby="hero-heading"
        >
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
                                        scale: [0.9, 1],
                                    },
                                    {
                                        duration: 0.5,
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="inline-flex items-center gap-2 rounded-full bg-emerald-100 px-4 py-1.5 text-sm font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300"
                >
                    <span class="relative flex size-2">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-emerald-500"></span>
                    </span>
                    Early Bird Pricing Available
                </div>

                {{-- Title --}}
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
                    class="mt-6 text-4xl font-bold md:text-5xl lg:text-6xl"
                >
                    <span class="text-gray-800 dark:text-white">The</span>
                    <span class="bg-gradient-to-r from-emerald-500 to-teal-500 bg-clip-text text-transparent">
                        NativePHP
                    </span>
                    <br />
                    <span class="text-gray-800 dark:text-white">Masterclass</span>
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
                    class="mx-auto mt-6 max-w-2xl text-lg text-gray-600 dark:text-gray-400"
                >
                    Go from zero to published app. Learn to build native mobile
                    and desktop applications using the PHP and Laravel skills you
                    already have.
                </p>

                {{-- CTA --}}
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
                                        delay: 0.2,
                                        ease: motion.easeOut,
                                    },
                                )
                            })
                        }
                    "
                    class="mt-8 flex flex-col items-center gap-4 sm:flex-row"
                >
                    @if ($alreadyOwned)
                        <div class="inline-flex items-center gap-2 rounded-xl bg-emerald-100 px-8 py-4 font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            You Own This Course
                        </div>
                    @else
                        <a
                            href="#pricing"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-8 py-4 font-semibold text-white transition hover:bg-emerald-700"
                        >
                            Get Early Bird Access &mdash; $101
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="size-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </a>
                        <a
                            href="#pricing"
                            class="text-sm font-medium text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            or start free
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Video --}}
        <section class="mt-16">
            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                $el,
                                {
                                    opacity: [0, 1],
                                    scale: [0.95, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-3xl overflow-hidden rounded-2xl shadow-xl"
            >
                <div class="relative w-full" style="padding-bottom: 56.25%">
                    <iframe
                        class="absolute inset-0 size-full"
                        src="https://www.youtube-nocookie.com/embed/HO9zbS2dM7M?rel=0&modestbranding=1"
                        title="The NativePHP Masterclass"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </section>

        {{-- Who It's For --}}
        <section
            class="mt-24"
            aria-labelledby="audience-heading"
        >
            <header class="text-center">
                <h2
                    id="audience-heading"
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
                    class="text-3xl font-semibold"
                >
                    Who Is This For?
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    This masterclass is built for developers who want to go
                    native without starting from scratch
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mt-10 grid gap-6 lg:grid-cols-3"
            >
                {{-- Persona 1 --}}
                <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-white p-8 ring-1 ring-emerald-200 dark:from-emerald-950/30 dark:to-gray-900 dark:ring-emerald-800/50">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/50 dark:text-emerald-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-6"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm14.25 6a.75.75 0 0 1-.22.53l-2.25 2.25a.75.75 0 1 1-1.06-1.06L15.44 12l-1.72-1.72a.75.75 0 1 1 1.06-1.06l2.25 2.25c.141.14.22.331.22.53Zm-10.28-.53a.75.75 0 0 0 0 1.06l2.25 2.25a.75.75 0 1 0 1.06-1.06L8.56 12l1.72-1.72a.75.75 0 1 0-1.06-1.06l-2.25 2.25Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-emerald-700 dark:text-emerald-400">
                        Laravel Developers
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        You already build web apps with Laravel. Now you want to
                        ship real native mobile and desktop apps &mdash; without
                        learning Swift, Kotlin, or Dart.
                    </p>
                </div>

                {{-- Persona 2 --}}
                <div class="rounded-2xl bg-gradient-to-br from-violet-50 to-white p-8 ring-1 ring-violet-200 dark:from-violet-950/30 dark:to-gray-900 dark:ring-violet-800/50">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/50 dark:text-violet-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-6"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-violet-700 dark:text-violet-400">
                        PHP Developers
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        You know PHP inside and out. This course shows you how
                        to leverage that expertise to build apps that run natively
                        on any device.
                    </p>
                </div>

                {{-- Persona 3 --}}
                <div class="rounded-2xl bg-gradient-to-br from-sky-50 to-white p-8 ring-1 ring-sky-200 dark:from-sky-950/30 dark:to-gray-900 dark:ring-sky-800/50">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600 dark:bg-sky-900/50 dark:text-sky-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="size-6"
                        >
                            <path d="M15.75 8.25a.75.75 0 0 1 .75.75c0 1.12-.492 2.126-1.27 2.812a.75.75 0 1 1-.992-1.124A2.243 2.243 0 0 0 15 9a.75.75 0 0 1 .75-.75Z" />
                            <path
                                fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM4.575 15.6a8.25 8.25 0 0 0 9.348 4.425 1.966 1.966 0 0 0-1.84-1.275.983.983 0 0 1-.97-.822l-.073-.437c-.094-.565.25-1.11.8-1.267l.99-.282c.427-.122.708-.53.654-.968a5.539 5.539 0 0 0-2.082-3.567 3.75 3.75 0 0 0-4.92.702 3.753 3.753 0 0 1-.847.849 1.5 1.5 0 0 1-2.078-.312c-.2-.267-.39-.542-.566-.826Zm5.507-8.373a.75.75 0 0 1 .468-.951 3.75 3.75 0 0 1 3.525.448 3.75 3.75 0 0 1-.463 6.535l-.972.486a.75.75 0 1 1-.67-1.342l.972-.486a2.25 2.25 0 0 0 .278-3.92 2.25 2.25 0 0 0-2.115-.27.75.75 0 0 1-.951-.467l.928-.033Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-sky-700 dark:text-sky-400">
                        Web Developers
                    </h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Tired of being told you need to learn a completely new
                        stack for native apps? This course proves you don't.
                    </p>
                </div>
            </div>
        </section>

        {{-- Meet Your Instructors --}}
        <section
            class="mt-24"
            aria-labelledby="instructors-heading"
        >
            <header class="text-center">
                <h2
                    id="instructors-heading"
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
                    class="text-3xl font-semibold"
                >
                    Meet Your Instructors
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    Learn from experienced developers and educators who built NativePHP
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    y: [30, 0],
                                    opacity: [0, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mx-auto mt-10 max-w-3xl space-y-8"
            >
                {{-- Shruti --}}
                <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 p-8 ring-1 ring-emerald-200 dark:from-emerald-950/30 dark:to-teal-950/30 dark:ring-emerald-800/50 sm:flex sm:items-start sm:gap-8">
                    <img
                        src="/img/shruti.jpg"
                        alt="Shruti Balasa"
                        class="mx-auto size-28 shrink-0 rounded-full object-cover sm:mx-0"
                    />
                    <div class="mt-4 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-xl font-semibold">Shruti Balasa</h3>
                        <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Your Tutor</p>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                            Shruti is a regular contributor to Laracasts, a veteran public speaker, and one of the best tech educators around. She brings clarity and warmth to even the most complex topics, making sure you never feel lost.
                        </p>
                    </div>
                </div>

                {{-- Simon & Shane --}}
                <p class="text-center text-sm font-medium text-gray-500 dark:text-gray-400">You'll also see...</p>
                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Simon --}}
                    <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                        <img
                            src="/img/team/simonhamp.jpg"
                            alt="Simon Hamp"
                            class="mx-auto size-20 rounded-full object-cover"
                        />
                        <h3 class="mt-4 text-lg font-semibold">Simon Hamp</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Co-creator of NativePHP</p>
                    </div>

                    {{-- Shane --}}
                    <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                        <img
                            src="/img/team/shanerosenthal.jpg"
                            alt="Shane Rosenthal"
                            class="mx-auto size-20 rounded-full object-cover"
                        />
                        <h3 class="mt-4 text-lg font-semibold">Shane Rosenthal</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Co-creator of NativePHP</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Pricing --}}
        <section
            id="pricing"
            class="mt-24"
            aria-labelledby="pricing-heading"
        >
            <header class="text-center">
                <h2
                    id="pricing-heading"
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
                    class="text-3xl font-semibold"
                >
                    Simple Pricing
                </h2>
                <p class="mx-auto mt-3 max-w-2xl text-gray-600 dark:text-gray-400">
                    One price. Full access. No subscriptions.
                </p>
            </header>

            <div
                x-init="
                    () => {
                        motion.inView($el, (element) => {
                            motion.animate(
                                Array.from($el.children),
                                {
                                    opacity: [0, 1],
                                    scale: [0.95, 1],
                                },
                                {
                                    duration: 0.7,
                                    ease: motion.backOut,
                                    delay: motion.stagger(0.12),
                                },
                            )
                        })
                    }
                "
                class="mx-auto mt-10 grid max-w-3xl gap-6 sm:grid-cols-2"
            >
                {{-- Free Tier --}}
                <div class="rounded-3xl bg-gray-100 p-8 ring-1 ring-gray-200 dark:bg-mirage dark:ring-white/10">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Free</p>
                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="text-5xl font-bold text-gray-900 dark:text-white">$0</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Forever free. No credit card required.</p>

                    <ul class="mt-8 space-y-3">
                        @foreach (['Intro modules & lessons', 'Community access', 'Course updates & notifications'] as $feature)
                            <li class="flex items-center gap-3">
                                <x-icons.checkmark class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @auth
                        <a href="{{ route('dashboard') }}" class="mt-8 flex w-full items-center justify-center rounded-xl border border-gray-300 px-6 py-4 text-sm font-semibold text-gray-700 transition hover:border-gray-400 dark:border-white/10 dark:text-gray-300 dark:hover:border-white/20">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('customer.register') }}" class="mt-8 flex w-full items-center justify-center rounded-xl border border-gray-300 px-6 py-4 text-sm font-semibold text-gray-700 transition hover:border-gray-400 dark:border-white/10 dark:text-gray-300 dark:hover:border-white/20">
                            Get Started
                        </a>
                    @endauth
                </div>

                {{-- Pro Tier --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-50 to-teal-50 p-8 ring-2 ring-emerald-300 dark:from-emerald-950/40 dark:to-teal-950/40 dark:ring-emerald-700">
                    <div class="absolute right-6 top-6 rounded-full bg-emerald-600 px-3 py-1 text-xs font-bold text-white">
                        EARLY BIRD
                    </div>

                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Pro</p>

                    @if ($alreadyOwned)
                        <div class="mt-6 text-center">
                            <div class="mx-auto grid size-14 place-items-center rounded-full bg-emerald-100 dark:bg-emerald-900/50">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-7 text-emerald-600 dark:text-emerald-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </div>
                            <p class="mt-3 text-lg font-semibold text-emerald-700 dark:text-emerald-300">You Own This Course</p>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">You have full access to all content.</p>
                        </div>
                    @else
                        <div class="mt-4 flex items-baseline gap-2">
                            <span class="text-5xl font-bold text-gray-900 dark:text-white">$101</span>
                            <span class="text-2xl text-gray-400 line-through">$299</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">one-time</span>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Early bird pricing. Lock it in now &mdash; full access forever.</p>
                    @endif

                    <ul class="mt-8 space-y-3">
                        @foreach (['Everything in Free', 'All current & future modules', 'Source code & project files', 'Lifetime access to all content', 'Access to private community'] as $feature)
                            <li class="flex items-center gap-3">
                                <x-icons.checkmark class="size-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>

                    @unless ($alreadyOwned)
                        <form
                            action="{{ route('course.checkout') }}"
                            method="POST"
                            class="mt-8"
                            id="checkout-form"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-8 py-4 text-center font-semibold text-white transition hover:bg-emerald-700"
                            >
                                Buy Now
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>

                        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                            Early bird pricing won't last forever. Lock in the lowest price today.
                        </p>
                    @endunless
                </div>
            </div>
        </section>

        {{-- Timeline / Availability --}}
        <section
            class="mt-24"
            aria-labelledby="timeline-heading"
        >
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
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="mx-auto max-w-2xl text-center"
            >
                <h2
                    id="timeline-heading"
                    class="text-3xl font-semibold"
                >
                    When Can I Start?
                </h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                    The NativePHP Masterclass is coming <strong class="text-gray-900 dark:text-white">Summer/Fall 2026</strong>.
                    We're putting the finishing touches on the content to make sure it's the best learning experience possible.
                </p>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Grab the early bird price now and you'll be first in line when the doors open.
                    Sign up below to stay in the loop.
                </p>
            </div>
        </section>

        {{-- Email Signup --}}
        <section
            id="signup"
            class="mt-24 pb-24"
            aria-labelledby="signup-heading"
        >
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
                                    duration: 0.7,
                                    ease: motion.easeOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gray-100 p-10 text-center dark:bg-mirage"
            >
                <h2
                    id="signup-heading"
                    class="text-2xl font-semibold"
                >
                    Not Ready to Buy?
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-gray-600 dark:text-gray-400">
                    Join the waitlist and we'll let you know when the masterclass
                    launches, plus get exclusive early access content.
                </p>

                <form
                    action="https://simonhamp.mailcoach.app/subscribe/67c51b1c-f7e3-4bbd-a6b8-e68dbed8c31f"
                    method="post"
                    class="mx-auto mt-8 flex max-w-md flex-col gap-3 sm:flex-row"
                >
                    <input
                        type="email"
                        name="email"
                        placeholder="you@example.com"
                        required
                        class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder-gray-400 focus:border-emerald-500 focus:ring-emerald-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                    />

                    {{-- Honeypot --}}
                    <div class="absolute -left-[9999px]">
                        <label for="website-honeypot">Your honeypot</label>
                        <input
                            type="text"
                            id="website-honeypot"
                            name="honeypot"
                            tabindex="-1"
                            autocomplete="nope"
                        />
                    </div>

                    <button
                        type="submit"
                        class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                    >
                        Join Waitlist
                    </button>
                </form>
            </div>
        </section>
    </div>

    @auth
        @if (request('checkout') && ! $alreadyOwned)
            <script>
                document.getElementById('checkout-form').submit();
            </script>
        @endif
    @endauth
</x-layout>
