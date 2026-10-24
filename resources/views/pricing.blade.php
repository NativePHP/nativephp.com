<x-layout title="NativePHP Ultra">
    @push('head')
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    {{-- Hero Section --}}
    <section
        class="mt-10 md:mt-14"
        aria-labelledby="hero-heading"
    >
        <header class="relative z-10 grid place-items-center text-center">
            {{-- Icon --}}
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
            >
                <div class="mx-auto grid size-20 place-items-center rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg">
                    <x-heroicon-s-bolt class="size-10" />
                </div>
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
                class="mt-6 text-4xl md:text-5xl lg:text-6xl"
            >
                NativePHP
                <span class="whitespace-nowrap"><span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">{</span>
                <span class="font-bold">Ultra</span>
                <span class="-mx-1.5 text-[#99ceb2] dark:text-indigo-500">}</span></span>
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
                class="mx-auto mt-6 max-w-3xl text-lg text-gray-600 md:text-xl dark:text-zinc-400"
            >
                Every first-party plugin, cheaper third-party plugins, an AI
                plugin dev kit, premium support and seats for your whole team.
                One subscription that gets you shipping faster.
            </p>

            {{-- Call to action --}}
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
                class="mt-8 flex flex-col items-center gap-4"
            >
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="#pricing"
                        class="inline-flex items-center gap-2 rounded-2xl bg-zinc-800 px-7 py-4 text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                    >
                        Get Ultra
                        <x-icons.right-arrow class="size-3" />
                    </a>
                    <a
                        href="#features"
                        class="inline-flex items-center gap-2 rounded-2xl bg-gray-100 px-7 py-4 text-sm font-medium transition duration-200 ease-in-out hover:bg-gray-200 dark:bg-mirage dark:hover:bg-slate-700/40"
                    >
                        See what's included
                    </a>
                </div>
                <p class="text-sm text-gray-500 dark:text-zinc-500">
                    From ${{ config('subscriptions.plans.max.price_monthly') }}/month
                    &middot; {{ config('subscriptions.plans.max.included_seats') }} seats included
                    &middot; Cancel anytime
                </p>
            </div>
        </header>
    </section>

    {{-- At a glance --}}
    <section
        class="mx-auto mt-20 max-w-5xl"
        aria-label="Ultra at a glance"
    >
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
            class="grid grid-cols-2 gap-4 lg:grid-cols-4"
        >
            <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                <div class="text-4xl font-semibold">
                    @if($includedPlugins->isNotEmpty())
                        {{ $includedPlugins->count() }}
                    @else
                        All
                    @endif
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-zinc-400">premium first-party plugins included</div>
            </div>
            <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                <div class="text-4xl font-semibold">~30%</div>
                <div class="mt-2 text-sm text-gray-600 dark:text-zinc-400">off third-party plugins in the Marketplace</div>
            </div>
            <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                <div class="text-4xl font-semibold">90%</div>
                <div class="mt-2 text-sm text-gray-600 dark:text-zinc-400">revenue share on plugins you sell</div>
            </div>
            <div class="rounded-2xl bg-gray-100 p-6 text-center dark:bg-mirage">
                <div class="text-4xl font-semibold">{{ config('subscriptions.plans.max.included_seats') }}</div>
                <div class="mt-2 text-sm text-gray-600 dark:text-zinc-400">team seats, so everyone gets the benefits</div>
            </div>
        </div>
    </section>

    {{-- First-party plugins --}}
    <section
        id="features"
        class="mx-auto mt-24 max-w-5xl scroll-mt-24"
        aria-labelledby="plugins-heading"
    >
        <div class="grid items-center gap-10 lg:grid-cols-2">
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
            >
                <div class="text-sm font-medium uppercase tracking-wider text-[#5b9e7c] dark:text-indigo-400">Plugins</div>
                <h2
                    id="plugins-heading"
                    class="mt-2 text-3xl font-semibold"
                >
                    Every first-party plugin. Included.
                </h2>
                <p class="mt-4 text-gray-600 dark:text-zinc-400">
                    Skip the checkout for each native feature you need. Ultra
                    unlocks every premium plugin built by the NativePHP team,
                    and new ones land in your account automatically the day
                    they ship.
                </p>

                <ul class="mt-6 space-y-3 text-sm">
                    <li class="flex items-center gap-2">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black" aria-hidden="true">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <span class="font-medium">Built and maintained by the core team</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black" aria-hidden="true">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <span class="font-medium">Future first-party plugins at no extra cost</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <div class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black" aria-hidden="true">
                            <x-icons.checkmark class="size-5 shrink-0" />
                        </div>
                        <span class="font-medium">Around 30% off third-party plugins too</span>
                    </li>
                </ul>

                @if($includedPluginsValue > 0)
                    <p class="mt-6 text-sm text-gray-600 dark:text-zinc-400">
                        Bought separately, today's first-party plugins cost
                        <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($includedPluginsValue) }}</span>.
                    </p>
                @endif
            </div>

            @if($includedPlugins->isNotEmpty())
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
                                        duration: 0.5,
                                        ease: motion.backOut,
                                        delay: motion.stagger(0.05),
                                    },
                                )
                            })
                        }
                    "
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                >
                    @foreach($includedPlugins->take(8) as $plugin)
                        <a
                            href="{{ route('plugins.show', $plugin->routeParams()) }}"
                            class="flex items-center gap-3 rounded-2xl bg-gray-100 p-3 transition duration-200 hover:bg-gray-200 dark:bg-mirage dark:hover:bg-slate-700/40"
                        >
                            @if($plugin->hasLogo())
                                <img src="{{ $plugin->getLogoUrl() }}" alt="" class="size-10 shrink-0 rounded-lg object-cover" />
                            @elseif($plugin->hasGradientIcon())
                                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white">
                                    <x-dynamic-component :component="$plugin->getIconComponent()" class="size-5" />
                                </div>
                            @else
                                <div class="grid size-10 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                    <x-vaadin-plug class="size-5" />
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="truncate text-sm font-medium">{{ $plugin->display_name ?? $plugin->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-zinc-500">
                                    @if($plugin->activePrice)
                                        <span class="line-through">${{ number_format($plugin->activePrice->amount / 100) }}</span>
                                    @endif
                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">Included</span>
                                </div>
                            </div>
                        </a>
                    @endforeach

                    @if($includedPlugins->count() > 8)
                        <a
                            href="{{ route('plugins') }}"
                            class="flex items-center justify-center gap-2 rounded-2xl bg-gray-100 p-3 text-sm font-medium transition duration-200 hover:bg-gray-200 sm:col-span-2 dark:bg-mirage dark:hover:bg-slate-700/40"
                        >
                            And {{ $includedPlugins->count() - 8 }} more
                            <x-icons.right-arrow class="size-3" />
                        </a>
                    @endif
                </div>
            @else
                <div class="grid place-items-center rounded-2xl bg-gray-100 p-10 text-center dark:bg-mirage">
                    <x-vaadin-plug class="size-12 text-gray-400 dark:text-zinc-500" />
                    <p class="mt-4 text-gray-600 dark:text-zinc-400">
                        Every premium plugin from the NativePHP team, now and in the future.
                    </p>
                </div>
            @endif
        </div>
    </section>

    {{-- Build & sell plugins --}}
    <section
        class="mx-auto mt-24 max-w-5xl"
        aria-labelledby="build-heading"
    >
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
            class="text-center"
        >
            <div class="text-sm font-medium uppercase tracking-wider text-[#5b9e7c] dark:text-indigo-400">For plugin authors</div>
            <h2
                id="build-heading"
                class="mt-2 text-3xl font-semibold"
            >
                Build plugins faster. Keep more of what you earn.
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-zinc-400">
                Ultra gives you the tools to wrap any native SDK as a NativePHP
                plugin, and a better deal when you sell it.
            </p>
        </div>

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
            class="mt-10 grid gap-6 md:grid-cols-2"
        >
            {{-- Plugin Dev Kit --}}
            <div class="flex flex-col rounded-2xl bg-zinc-800 p-8 text-white dark:bg-indigo-700/80">
                <div class="grid size-12 place-items-center rounded-xl bg-white/10">
                    <x-heroicon-o-command-line class="size-6" />
                </div>
                <h3 class="mt-6 text-2xl font-semibold">Claude Code Plugin Dev Kit</h3>
                <p class="mt-3 text-gray-300 dark:text-indigo-100">
                    Specialist agents and skills that know NativePHP's bridge
                    inside out. Describe the native feature you want, and get
                    Kotlin, Swift, JavaScript and PHP that follow our
                    conventions, plus validation to catch mistakes before you
                    ship.
                </p>
                <div class="mt-auto pt-6">
                    <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-medium">Free with Ultra</span>
                </div>
            </div>

            {{-- Revenue share --}}
            <div class="flex flex-col rounded-2xl bg-gray-100 p-8 dark:bg-mirage">
                <div class="grid size-12 place-items-center rounded-xl bg-[#cbe7d8] dark:bg-slate-800">
                    <x-heroicon-o-banknotes class="size-6" />
                </div>
                <h3 class="mt-6 text-2xl font-semibold">Up to 90% revenue share</h3>
                <p class="mt-3 text-gray-600 dark:text-zinc-400">
                    Publish paid plugins to the Marketplace and keep up to 90%
                    of every sale. We handle checkout, licensing and payouts,
                    so you can focus on building.
                </p>
                <div class="mt-auto pt-6">
                    <a
                        href="{{ route('plugins') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium transition duration-200 hover:translate-x-1"
                    >
                        Explore the Marketplace
                        <x-icons.right-arrow class="size-3" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Teams --}}
    <section
        class="mx-auto mt-24 max-w-5xl"
        aria-labelledby="teams-heading"
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
            class="grid items-center gap-10 rounded-2xl bg-gray-100 p-8 md:grid-cols-5 md:p-12 dark:bg-mirage"
        >
            <div class="md:col-span-3">
                <div class="text-sm font-medium uppercase tracking-wider text-[#5b9e7c] dark:text-indigo-400">Teams</div>
                <h2
                    id="teams-heading"
                    class="mt-2 text-3xl font-semibold"
                >
                    Bring your whole team
                </h2>
                <p class="mt-4 text-gray-600 dark:text-zinc-400">
                    Ultra comes with {{ config('subscriptions.plans.max.included_seats') }} seats.
                    Invite your teammates and they get the same plugins,
                    discounts and support as you. Add or remove people
                    whenever you like.
                </p>
                <p class="mt-4 text-sm text-gray-500 dark:text-zinc-500">
                    Need more seats? Add them for ${{ config('subscriptions.plans.max.extra_seat_price_monthly') }}/mo each,
                    or ${{ config('subscriptions.plans.max.extra_seat_price_yearly') }}/mo on annual plans.
                </p>
            </div>

            <div
                class="flex justify-center md:col-span-2"
                aria-hidden="true"
            >
                <div class="flex -space-x-3">
                    @foreach(range(1, config('subscriptions.plans.max.included_seats')) as $seat)
                        <div class="grid size-14 place-items-center rounded-full bg-white ring-4 ring-gray-100 dark:bg-slate-800 dark:ring-mirage">
                            <x-heroicon-s-user class="size-6 text-gray-400 dark:text-zinc-500" />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Everything else --}}
    <section
        class="mx-auto mt-24 max-w-5xl"
        aria-labelledby="perks-heading"
    >
        <h2
            id="perks-heading"
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
            And a whole lot more
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
            class="mt-16 grid gap-x-8 gap-y-12 md:grid-cols-2 lg:grid-cols-3"
        >
            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-lifebuoy class="size-6" />
                </x-slot>
                <x-slot name="title">Premium support</x-slot>
                <x-slot name="description">
                    Private support channels and expedited turnaround, so a
                    blocker doesn't hold up your release.
                </x-slot>
            </x-benefit-card>

            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-code-bracket class="size-6" />
                </x-slot>
                <x-slot name="title">Direct repo access</x-slot>
                <x-slot name="description">
                    Get into the NativePHP source repositories on GitHub and see
                    exactly how everything works.
                </x-slot>
            </x-benefit-card>

            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-map class="size-6" />
                </x-slot>
                <x-slot name="title">Shape the roadmap</x-slot>
                <x-slot name="description">
                    Have a say in feature priority and influence what the core
                    team builds next.
                </x-slot>
            </x-benefit-card>

            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-shopping-bag class="size-6" />
                </x-slot>
                <x-slot name="title">Cheaper Marketplace plugins</x-slot>
                <x-slot name="description">
                    Pay around 30% less for third-party plugins. The discount is
                    applied automatically at checkout.
                </x-slot>
            </x-benefit-card>

            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-academic-cap class="size-6" />
                </x-slot>
                <x-slot name="title">Course &amp; app discounts</x-slot>
                <x-slot name="description">
                    Subscriber pricing on NativePHP courses, apps and future
                    products as they launch.
                </x-slot>
            </x-benefit-card>

            <x-benefit-card>
                <x-slot name="icon">
                    <x-heroicon-o-user-group class="size-6" />
                </x-slot>
                <x-slot name="title">Discounted consulting</x-slot>
                <x-slot name="description">
                    A lower hourly rate when you
                    <a href="{{ route('consulting') }}" class="underline hover:text-gray-900 dark:hover:text-white">book time with the core team</a>
                    for architecture, reviews or launch help.
                </x-slot>
            </x-benefit-card>
        </div>
    </section>

    {{-- Pays for itself --}}
    @if($includedPluginsValue > config('subscriptions.plans.max.price_yearly'))
        <section
            class="mx-auto mt-24 max-w-3xl"
            aria-label="Ultra value"
        >
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
                                    ease: motion.backOut,
                                },
                            )
                        })
                    }
                "
                class="rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 p-8 text-center text-white md:p-12"
            >
                <h2 class="text-3xl font-semibold">Ultra pays for itself</h2>
                <p class="mx-auto mt-4 max-w-xl text-lg text-white/90">
                    The first-party plugins alone would cost
                    <span class="font-semibold text-white">${{ number_format($includedPluginsValue) }}</span>
                    to buy separately. A year of Ultra is
                    <span class="font-semibold text-white">${{ config('subscriptions.plans.max.price_yearly') }}</span>,
                    and that's before support, seats and everything else.
                </p>
            </div>
        </section>
    @endif

    {{-- FAQ Section --}}
    <section
        class="mt-24"
        aria-labelledby="faq-heading"
    >
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
            <x-faq-card question="What is NativePHP Ultra?">
                <p>
                    NativePHP Ultra is a premium subscription that gives you
                    access to all first-party plugins, around 30% off
                    third-party plugins, the Claude Code
                    Plugin Dev Kit, discounts on NativePHP courses and
                    apps, Teams support, premium support through private channels with expedited
                    turnaround times, and up to 90% revenue share on
                    paid plugins you publish to the Marketplace.
                </p>
            </x-faq-card>

            <x-faq-card question="Do I need Ultra to use NativePHP?">
                <p>
                    No! NativePHP for Mobile is completely free. Ultra is
                    an optional premium subscription for developers who
                    want access to additional tools, plugins, and
                    priority support.
                </p>
            </x-faq-card>

            <x-faq-card question="What first-party plugins are included?">
                <p>
                    All first-party NativePHP plugins are included with
                    your Ultra subscription at no additional cost. As we
                    release new first-party plugins, they will be
                    automatically available to you.
                </p>
            </x-faq-card>

            <x-faq-card question="What is the Claude Code Plugin Dev Kit?">
                <p>
                    The Claude Code Plugin Dev Kit is a set of tools and
                    resources that help you build NativePHP plugins using
                    Claude Code. It's available for free to Ultra
                    subscribers.
                </p>
            </x-faq-card>

            <x-faq-card question="What discounts do I get on courses and apps?">
                <p>
                    Ultra subscribers get discounts on NativePHP courses
                    and apps. As we release new educational content and
                    tools, you'll automatically be eligible for
                    subscriber pricing.
                </p>
            </x-faq-card>

            <x-faq-card question="How does Teams support work?">
                <p>
                    Ultra includes Teams support, which lets you invite
                    other users into your team so they can share your
                    plugins and other Ultra benefits. As the account
                    owner, you can manage your team members and remove
                    access at any time.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I purchase additional team seats?">
                <p>
                    Ultra includes {{ config('subscriptions.plans.max.included_seats') }} team seats. If you need more, extra
                    seats can be purchased from your team settings page
                    at ${{ config('subscriptions.plans.max.extra_seat_price_monthly') }}/mo per seat on monthly plans or ${{ config('subscriptions.plans.max.extra_seat_price_yearly') }}/mo per seat
                    on annual plans. Extra seats are billed pro-rata to
                    match your subscription cycle.
                </p>
            </x-faq-card>

            <x-faq-card question="What does premium support include?">
                <p>
                    Premium support gives you access to private support channels
                    with expedited turnaround on your issues. When you need
                    help, your requests are prioritized so you can get back
                    to building faster.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I switch between monthly and annual billing?">
                <p>
                    You can manage your billing via the Stripe billing
                    portal. If you'd like to switch between monthly and
                    annual, you can cancel your current subscription and
                    start a new one on the billing interval you prefer.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I cancel my subscription?">
                <p>
                    Yes, you can cancel at any time. You'll continue to
                    have access to Ultra benefits until the end of your
                    current billing period.
                </p>
                <p class="mt-2">
                    After cancellation, you'll retain access to any
                    plugins you've purchased through the Marketplace.
                    However, free access to first-party plugins and
                    team member benefits will end when the subscription
                    expires.
                </p>
            </x-faq-card>

            <x-faq-card question="Can I get an invoice?">
                <p>
                    Yes, invoices are sent automatically with your receipt
                    via email after each payment.
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

    {{-- Pricing Section --}}
    <div class="pb-24">
        <livewire:mobile-pricing />
    </div>
</x-layout>
