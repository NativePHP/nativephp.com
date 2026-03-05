<section
    id="pricing"
    class="mx-auto mt-24 max-w-2xl"
    aria-labelledby="pricing-heading"
>
    <header class="relative z-10 grid place-items-center text-center">
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
            Choose your plan
        </h2>

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
            Get the most out of NativePHP
        </p>
    </header>

    {{-- Interval Toggle --}}
    <div
        x-data="{ interval: @entangle('interval') }"
        class="mt-8 flex flex-col items-center gap-6"
    >
        <div
            class="inline-flex items-center gap-1 rounded-full bg-gray-100 p-1 dark:bg-mirage"
            role="radiogroup"
            aria-label="Billing interval"
        >
            <button
                type="button"
                @click="interval = 'month'"
                :class="interval === 'month'
                    ? 'bg-white text-black shadow-sm dark:bg-zinc-700 dark:text-white'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="rounded-full px-5 py-2 text-sm font-medium transition"
                role="radio"
                :aria-checked="interval === 'month'"
            >
                Monthly
            </button>
            <button
                type="button"
                @click="interval = 'year'"
                :class="interval === 'year'
                    ? 'bg-white text-black shadow-sm dark:bg-zinc-700 dark:text-white'
                    : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="relative rounded-full px-5 py-2 text-sm font-medium transition"
                role="radio"
                :aria-checked="interval === 'year'"
            >
                Annual
                <span class="absolute -right-2 -top-2.5 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                    Save 16%
                </span>
            </button>
        </div>

        {{-- Ultra Plan Card --}}
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
                                ease: motion.circOut,
                            },
                        )
                    })
                }
            "
            class="w-full rounded-2xl bg-gray-100 p-7 opacity-0 ring-1 ring-black dark:bg-black/50 dark:ring-white/20"
            aria-labelledby="ultra-plan-heading"
        >
            {{-- Plan Name --}}
            <h3
                id="ultra-plan-heading"
                class="text-3xl font-semibold"
            >
                <span class="rounded-full bg-zinc-300 px-4 dark:bg-zinc-600">
                    Ultra
                </span>
            </h3>

            {{-- Price --}}
            <div
                class="flex items-start gap-1.5 pt-5"
                :aria-label="interval === 'month'
                    ? 'Price: $35 per month'
                    : 'Price: $350 per year'"
            >
                <div class="text-5xl font-semibold">
                    $<span x-text="interval === 'month' ? '35' : '350'"></span>
                </div>
                <div class="self-end pb-1.5 text-zinc-500">
                    <span x-text="interval === 'month' ? 'per month' : 'per year'"></span>
                </div>
            </div>

            {{-- Savings note --}}
            <div
                x-show="interval === 'year'"
                x-transition
                class="pt-1 text-sm text-emerald-600 dark:text-emerald-400"
            >
                Save $70/year vs monthly
            </div>

            {{-- CTA Button --}}
            @auth
                <button
                    type="button"
                    wire:click="createCheckoutSession('max')"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                    aria-label="Get started with Ultra plan"
                >
                    Get started
                </button>
            @else
                <button
                    type="button"
                    @click="$dispatch('open-purchase-modal', { plan: 'max' })"
                    class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                    aria-label="Get started with Ultra plan"
                >
                    Get started
                </button>
            @endauth

            {{-- Features --}}
            <div
                class="space-y-2.5 text-sm"
                aria-label="Ultra plan features"
            >
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">All first-party plugins for free</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Discounts on third-party plugins</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Claude Code Plugin Dev Kit for free</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Discounts on NativePHP courses and apps</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Premium support &mdash; expedited turnaround</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Teams support &mdash; invite and manage members</div>
                </div>
            </div>
        </div>
    </div>

    @guest
        <livewire:purchase-modal />
    @endguest
</section>
