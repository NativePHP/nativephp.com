<x-layout title="Consulting">
    @push('head')
        <style>html { scroll-behavior: smooth; }</style>
        @if (config('services.turnstile.site_key'))
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif
    @endpush

    <div class="mx-auto max-w-5xl">
        {{-- Hero --}}
        <section class="mt-12">
            <div class="text-center">
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
                    class="text-4xl md:text-5xl"
                >
                    <span class="text-[#99ceb2] dark:text-indigo-500">{</span>
                    <span class="font-bold">Consulting</span>
                    <span class="text-[#99ceb2] dark:text-indigo-500">}</span>
                </h1>

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
                    Work directly with the NativePHP core team. We'll guide your project from strategy to shipping.
                </p>
            </div>
        </section>

        {{-- Meet the Team --}}
        <section class="mt-16">
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
                class="text-center text-3xl font-semibold"
            >
                Meet the Team
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
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="mx-auto mt-10 grid max-w-3xl gap-8 md:grid-cols-2"
            >
                {{-- Simon --}}
                <div class="rounded-2xl bg-gray-100 p-8 text-center dark:bg-[#1a1a2e]">
                    <img src="/img/team/simonhamp.jpg" alt="Simon Hamp" class="mx-auto size-24 rounded-full object-cover" />
                    <h3 class="mt-4 text-xl font-semibold">Simon Hamp</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Co-creator of NativePHP</p>
                    <p class="mt-3 text-sm text-gray-600 dark:text-zinc-400">
                        Full-stack developer, engineering leader and entrepreneur with over 20 years of experience building web and mobile products.
                        Simon developed the pioneering technology which became NativePHP Desktop and NativePHP Mobile.
                        He has built a career on growing startups and supporting large enterprises with PHP and Laravel.
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="https://github.com/simonhamp" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Simon Hamp on GitHub">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0 1 12 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12Z"/></svg>
                        </a>
                        <a href="https://x.com/simonhamp" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Simon Hamp on X">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://linkedin.com/in/simonhamp" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Simon Hamp on LinkedIn">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Shane --}}
                <div class="rounded-2xl bg-gray-100 p-8 text-center dark:bg-[#1a1a2e]">
                    <img src="/img/team/shanerosenthal.jpg" alt="Shane Rosenthal" class="mx-auto size-24 rounded-full object-cover" />
                    <h3 class="mt-4 text-xl font-semibold">Shane Rosenthal</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Co-creator of NativePHP</p>
                    <p class="mt-3 text-sm text-gray-600 dark:text-zinc-400">
                        Full-stack developer, DevOps-minded systems architect, and entrepreneur with over 15 years of experience.
                        Shane has consistently broken boundaries, pushing NativePHP Mobile to new heights. He brings deep expertise
                        in PHP and Laravel, along with a strong foundation in building and scaling complex systems.
                    </p>
                    <div class="mt-4 flex items-center justify-center gap-3">
                        <a href="https://github.com/shanerbaner82" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Shane Rosenthal on GitHub">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0 1 12 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12Z"/></svg>
                        </a>
                        <a href="https://x.com/shanedrosenthal" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Shane Rosenthal on X">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a href="https://linkedin.com/in/shane-rosenthal" target="_blank" rel="noopener noreferrer" class="text-gray-400 transition duration-200 hover:text-gray-600 dark:hover:text-gray-300" aria-label="Shane Rosenthal on LinkedIn">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- What We Help With --}}
        <section class="mt-24">
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
                class="text-center text-3xl font-semibold"
            >
                What We Help With
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
                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Mobile Apps</x-slot>
                    <x-slot name="description">
                        Architecture, performance, and App Store strategy for NativePHP Mobile projects on iOS and Android.
                    </x-slot>
                </x-benefit-card>

                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Desktop Apps</x-slot>
                    <x-slot name="description">
                        Windows, macOS, and Linux desktop applications using NativePHP Desktop.
                    </x-slot>
                </x-benefit-card>

                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Strategy &amp; Planning</x-slot>
                    <x-slot name="description">
                        Feasibility studies, technology selection, and roadmap planning for your product vision.
                    </x-slot>
                </x-benefit-card>

                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Marketing &amp; Launch</x-slot>
                    <x-slot name="description">
                        App Store optimisation, launch strategy, and go-to-market advice from people who've done it.
                    </x-slot>
                </x-benefit-card>

                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286ZM12 9v3.75m0 0v.008" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Risk &amp; Compliance</x-slot>
                    <x-slot name="description">
                        Security reviews, App Store guideline compliance, and risk assessment for your project.
                    </x-slot>
                </x-benefit-card>

                <x-benefit-card>
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>
                    </x-slot>
                    <x-slot name="title">Code Review &amp; Architecture</x-slot>
                    <x-slot name="description">
                        Expert review of your existing NativePHP codebase with actionable recommendations.
                    </x-slot>
                </x-benefit-card>
            </div>
        </section>

        {{-- How We Work --}}
        <section class="mt-24">
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
                class="rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-12"
            >
                <h2 class="text-3xl font-semibold">How We Work</h2>
                <div class="mt-6 space-y-4 text-gray-600 dark:text-zinc-400">
                    <p>
                        We support teams at every stage of the product cycle &mdash; from shaping an early idea through to
                        joining mid-flight projects, blending seamlessly with your existing team.
                    </p>
                    <p>
                        We've worked with all kinds of organisations, from indie developers and small funded startups, to large enterprises including blue-chip and pharmaceutical companies.
                    </p>
                    <p>
                        We bill hourly and scope every engagement to your needs. Whether you need a half-day architecture
                        review, regular guidance and advice, or daily delivery, we can tailor the arrangement to fit.
                    </p>
                    <p>
                        Every engagement begins with a free, no-commitments discovery call so we can understand your goals, assess feasibility,
                        and outline a clear path forward.
                    </p>
                </div>
            </div>
        </section>

        {{-- Three Pathways --}}
        <section class="mt-24">
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
                                    delay: motion.stagger(0.15),
                                },
                            )
                        })
                    }
                "
                class="grid gap-6 md:grid-cols-3"
            >
                {{-- Book a Consultation --}}
                <a
                    href="#enquiry-form"
                    class="group rounded-2xl bg-zinc-800 p-8 text-white transition duration-200 hover:bg-zinc-900 dark:bg-indigo-700/80 dark:hover:bg-indigo-900"
                >
                    <h3 class="text-xl font-semibold">Book a Consultation</h3>
                    <p class="mt-2 text-sm text-gray-300 dark:text-indigo-200">
                        Tell us about your project and we'll arrange a discovery call.
                    </p>
                    <div class="mt-4 inline-flex items-center gap-2 text-sm font-medium transition duration-200 group-hover:translate-x-1">
                        Get started
                        <x-icons.right-arrow class="size-3" />
                    </div>
                </a>

                {{-- Need Support? --}}
                <a
                    href="{{ route('support.index') }}"
                    class="group rounded-2xl bg-gray-100 p-8 transition duration-200 hover:bg-gray-200 dark:bg-[#1a1a2e] dark:hover:bg-[#1a1a2e]/80"
                >
                    <h3 class="text-xl font-semibold">Need Support?</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                        Get help with NativePHP through our dedicated support channels.
                    </p>
                    <div class="mt-4 inline-flex items-center gap-2 text-sm font-medium transition duration-200 group-hover:translate-x-1">
                        Visit support
                        <x-icons.right-arrow class="size-3" />
                    </div>
                </a>

                {{-- Agency Partner --}}
                <a
                    href="#agency-partners"
                    class="group rounded-2xl bg-gray-100 p-8 transition duration-200 hover:bg-gray-200 dark:bg-[#1a1a2e] dark:hover:bg-[#1a1a2e]/80"
                >
                    <h3 class="text-xl font-semibold">Work with an Agency</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                        Connect with a vetted NativePHP agency partner for your project.
                    </p>
                    <div class="mt-4 inline-flex items-center gap-2 text-sm font-medium transition duration-200 group-hover:translate-x-1">
                        See partners
                        <x-icons.right-arrow class="size-3" />
                    </div>
                </a>
            </div>
        </section>

        {{-- Enquiry Form --}}
        <section id="enquiry-form" class="mt-24 scroll-mt-24">
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
                class="text-center text-3xl font-semibold"
            >
                Request a Consultation
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                Tell us about your project and we'll be in touch to arrange a discovery call.
            </p>

            <div class="mx-auto mt-8 max-w-2xl rounded-2xl bg-gray-100 p-8 dark:bg-[#1a1a2e] md:p-12">
                <livewire:lead-submission-form />
            </div>
        </section>

        {{-- Agency Partners --}}
        <section id="agency-partners" class="mt-24 scroll-mt-24 pb-24">
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
                class="text-center text-3xl font-semibold"
            >
                Agency Partners
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-center text-gray-600 dark:text-zinc-400">
                Need something more specialized? These vetted agencies have deep NativePHP experience and can help bring your project to life.
            </p>

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
                class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2"
            >
                <x-home.featured-partner-card
                    partnerName="Nexcalia"
                    tagline="Smart tools for scheduling & visitor management"
                    href="https://www.nexcalia.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.nexcalia
                            class="text-black dark:text-white"
                            aria-hidden="true"
                        />
                    </x-slot>
                    <x-slot:description>
                        From online booking to interactive kiosks, Nexcalia helps businesses streamline appointments and improve customer experiences.
                    </x-slot>
                </x-home.featured-partner-card>

                <x-home.featured-partner-card
                    partnerName="Web Mavens"
                    tagline="Build Secure, Scalable Web Apps"
                    href="https://www.webmavens.com/?ref=nativephp"
                >
                    <x-slot:logo>
                        <x-sponsors.logos.webmavens
                            class="dark:fill-white"
                            aria-hidden="true"
                        />
                    </x-slot>
                    <x-slot:description>
                        Laravel Partners crafting secure, SOC 2-ready apps with NativePHP and modern web technologies.
                    </x-slot>
                </x-home.featured-partner-card>

                <x-home.featured-partner-card
                    partnerName="Synergi Tech"
                    tagline="Bespoke software for complex infrastructure"
                    href="https://synergitech.co.uk/partners/nativephp/"
                >
                    <x-slot:logo>
                        <img
                            src="/img/sponsors/synergi.svg"
                            class="block dark:hidden"
                            loading="lazy"
                            alt="Synergi Tech logo"
                            width="160"
                            height="40"
                        />
                        <img
                            src="/img/sponsors/synergi-dark.svg"
                            class="hidden dark:block"
                            loading="lazy"
                            alt="Synergi Tech logo"
                            width="160"
                            height="40"
                        />
                    </x-slot>
                    <x-slot:description>
                        Synergi Tech are an established bespoke software development agency in the UK, specialising in business management and high-growth, complex infrastructure.
                    </x-slot>
                </x-home.featured-partner-card>

            </div>

            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Interested in becoming an agency partner?
                <a href="{{ route('partners') }}" class="font-medium text-blue-600 hover:underline dark:text-blue-400">
                    Join our Partner Program
                </a>
                for discounted rates on consultation and to get listed here.
            </p>
        </section>
    </div>
</x-layout>
