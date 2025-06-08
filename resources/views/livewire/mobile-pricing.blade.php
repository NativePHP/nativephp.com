<section
    class="mx-auto mt-24 max-w-6xl px-5"
    aria-labelledby="pricing-heading"
>
    <header class="relative z-10 grid place-items-center text-center">
        {{-- Section Heading --}}
        <h2
            id="pricing-heading"
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
            class="mx-auto max-w-xl pt-2 text-base/relaxed text-gray-600 opacity-0"
        >
            Start your journey to become a mobile developer
        </p>
    </header>

    {{-- Pricing Plans --}}
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
                            ease: motion.circOut,
                            delay: motion.stagger(0.1),
                        },
                    )
                })
            }
        "
        class="mt-10 grid grid-cols-[repeat(auto-fill,minmax(19rem,1fr))] items-start gap-x-6 gap-y-8"
        aria-label="Pricing plans"
    >
        {{-- Mini Plan --}}
        <div
            class="h-full rounded-2xl bg-gray-100 p-7 opacity-0 dark:bg-mirage"
            aria-labelledby="pro-plan-heading"
        >
            {{-- Plan Name --}}
            <h3
                id="pro-plan-heading"
                class="text-2xl font-semibold"
            >
                Mini
            </h3>

            {{-- Price --}}
            <div
                class="flex items-start gap-1.5 pt-5"
                aria-label="Price: $100 per year"
            >
                <div class="text-5xl font-semibold">
                    ${{ number_format(100) }}
                </div>
                <div class="self-end pb-1.5 text-zinc-500">per year</div>
            </div>

            @auth
                <button
                    type="button"
                    wire:click="createCheckoutSession('mini')"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                    aria-label="Get started with Mini plan"
                >
                    Get started
                </button>
            @else
                <button
                    type="button"
                    @click="$dispatch('open-purchase-modal', { plan: 'mini' })"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                    aria-label="Get started with Mini plan"
                >
                    Get started
                </button>
            @endauth

            {{-- Features --}}
            <div
                class="space-y-3 text-sm"
                aria-label="Mini plan features"
            >
                <div class="flex items-center gap-2">
                    <x-icons.desktop-computer
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Build
                        <span class="font-medium text-black dark:text-white">
                            unlimited
                        </span>
                        apps
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.upload-box
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Release
                        <span class="font-medium text-black dark:text-white">
                            1
                        </span>
                        production app
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.user-single
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        <span class="font-medium text-black dark:text-white">
                            1
                        </span>
                        developer seat
                    </div>
                </div>
            </div>

            {{-- Divider - Decorative --}}
            <div
                class="my-5 h-px w-full rounded-full bg-black/15"
                aria-hidden="true"
            ></div>

            {{-- Perks --}}
            <div
                class="space-y-2.5 text-sm"
                aria-label="Mini plan perks"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Community support via Discord</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Repo access</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Help decide feature priority</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Business hours email support (GMT)</div>
                </div>
            </div>
        </div>

        {{-- Pro Plan --}}
        <div
            class="h-full rounded-2xl bg-gray-100 p-7 opacity-0 dark:bg-mirage"
            aria-labelledby="teams-plan-heading"
        >
            {{-- Plan Name --}}
            <h3
                id="teams-plan-heading"
                class="text-2xl font-semibold"
            >
                Pro
            </h3>

            {{-- Price --}}
            <div
                class="flex items-start gap-1.5 pt-5"
                aria-label="Price: $750 per year"
            >
                <div class="text-5xl font-semibold">
                    ${{ number_format(750) }}
                </div>
                <div class="self-end pb-1.5 text-zinc-500">per year</div>
            </div>

            @auth
                <button
                    type="button"
                    wire:click="createCheckoutSession('pro')"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                    aria-label="Get started with Pro plan"
                >
                    Get started
                </button>
            @else
                <button
                    type="button"
                    @click="$dispatch('open-purchase-modal', { plan: 'pro' })"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                    aria-label="Get started with Pro plan"
                >
                    Get started
                </button>
            @endauth

            {{-- Features --}}
            <div
                class="space-y-3 text-sm"
                aria-label="Pro plan features"
            >
                <div class="flex items-center gap-2">
                    <x-icons.desktop-computer
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Build
                        <span class="font-medium text-black dark:text-white">
                            unlimited
                        </span>
                        apps
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.upload-box
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Release
                        <span class="font-medium text-black dark:text-white">
                            10
                        </span>
                        production apps
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.user-single
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        <span class="font-medium text-black dark:text-white">
                            10
                        </span>
                        developer seats
                    </div>
                </div>
            </div>

            {{-- Divider - Decorative --}}
            <div
                class="my-5 h-px w-full rounded-full bg-black/15"
                aria-hidden="true"
            ></div>

            {{-- Perks --}}
            <div
                class="space-y-2.5 text-sm"
                aria-label="Pro plan perks"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Community support via Discord</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Repo access</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Help decide feature priority</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-zinc-200 dark:bg-gray-700/50"
                        aria-hidden="true"
                    >
                        <x-icons.xmark
                            class="size-2.5 shrink-0 dark:opacity-70"
                        />
                    </div>
                    <div>Business hours email support (GMT)</div>
                </div>
            </div>
        </div>

        {{-- Max Plan - Most Popular --}}
        <div
            class="relative h-full rounded-2xl bg-gray-100 p-7 opacity-0 ring-1 ring-black dark:bg-black/50 dark:ring-white/20"
            aria-labelledby="max-plan-heading"
        >
            {{-- Popular badge --}}
            <div
                class="absolute -right-3 -top-5 rounded-xl bg-linear-to-tr from-[#6886FF] to-[#B8C1FF] px-5 py-2 text-sm text-white dark:from-gray-900 dark:to-black dark:ring-1 dark:ring-white/10"
                aria-label="Most popular plan"
            >
                Most Popular
            </div>

            {{-- Plan Name --}}
            <h3
                id="max-plan-heading"
                class="text-2xl font-semibold"
            >
                Max
            </h3>

            {{-- Price --}}
            <div
                class="flex items-start gap-1.5 pt-5"
                aria-label="Price: $2,500 per year"
            >
                <div class="text-5xl font-semibold">
                    ${{ number_format(2500) }}
                </div>
                <div class="self-end pb-1.5 text-zinc-500">per year</div>
            </div>

            @auth
                <button
                    type="button"
                    wire:click="createCheckoutSession('max')"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#d68ffe] dark:text-black dark:hover:bg-[#e1acff]"
                    aria-label="Get started with Max plan"
                >
                    Get started
                </button>
            @else
                <button
                    type="button"
                    @click="$dispatch('open-purchase-modal', { plan: 'max' })"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-900 dark:bg-[#d68ffe] dark:text-black dark:hover:bg-[#e1acff]"
                    aria-label="Get started with Max plan"
                >
                    Get started
                </button>
            @endauth

            {{-- Features --}}
            <div
                class="space-y-3 text-sm"
                aria-label="Max plan features"
            >
                <div class="flex items-center gap-2">
                    <x-icons.desktop-computer
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Build
                        <span class="font-medium text-black dark:text-white">
                            unlimited
                        </span>
                        apps
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.upload-box
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        Release
                        <span class="font-medium text-black dark:text-white">
                            unlimited
                        </span>
                        production apps
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <x-icons.user-single
                        class="size-5 shrink-0"
                        aria-hidden="true"
                    />
                    <div class="text-zinc-500">
                        <span class="font-medium text-black dark:text-white">
                            Unlimited
                        </span>
                        developer seats
                    </div>
                </div>
            </div>

            {{-- Divider - Decorative --}}
            <div
                class="my-5 h-px w-full rounded-full bg-black/15"
                aria-hidden="true"
            ></div>

            {{-- Perks --}}
            <div
                class="space-y-2.5 text-sm"
                aria-label="Max plan perks"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">
                        Private Discord channels for rapid support
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Repo access</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Help decide feature priority</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Business hours email support (GMT)</div>
                </div>
            </div>
        </div>
    </div>
    @guest
        <livewire:purchase-modal />
    @endguest
</section>
