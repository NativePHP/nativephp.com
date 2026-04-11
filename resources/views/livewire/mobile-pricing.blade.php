<section
    id="pricing"
    class="mx-auto mt-24 max-w-2xl"
    aria-labelledby="pricing-heading"
>
    <header class="relative z-10 grid place-items-center text-center">
        <h2
            wire:ignore
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
            wire:ignore
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
        x-data="{ interval: @entangle('interval'), showUpgradeModal: false }"
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
                Yearly
                <span class="absolute -right-2 -top-2.5 rounded-full bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                    @if($isEapCustomer)
                        EAP offer
                    @else
                        Save 16%
                    @endif
                </span>
            </button>
        </div>

        {{-- Ultra Plan Card --}}
        <div
            wire:ignore.self
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
            @if($isEapCustomer)
                <div class="flex items-start gap-1.5 pt-5">
                    <div class="text-5xl font-semibold">
                        $<span x-text="interval === 'month' ? '{{ config('subscriptions.plans.max.price_monthly') }}' : '{{ $eapYearlyPrice }}'"></span>
                    </div>
                    <div class="self-end pb-1.5 text-zinc-500">
                        <span x-text="interval === 'month' ? 'per month' : 'per year'"></span>
                    </div>
                </div>
                <div
                    x-show="interval === 'year'"
                    x-transition
                    class="flex items-center gap-2 pt-1"
                >
                    <span class="text-lg text-zinc-400 line-through">${{ $regularYearlyPrice }}/yr</span>
                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                        {{ $eapDiscountPercent }}% off
                    </span>
                </div>
            @else
                <div
                    class="flex items-start gap-1.5 pt-5"
                    :aria-label="interval === 'month'
                        ? 'Price: ${{ config('subscriptions.plans.max.price_monthly') }} per month'
                        : 'Price: ${{ config('subscriptions.plans.max.price_yearly') }} per year'"
                >
                    <div class="text-5xl font-semibold">
                        $<span x-text="interval === 'month' ? '{{ config('subscriptions.plans.max.price_monthly') }}' : '{{ config('subscriptions.plans.max.price_yearly') }}'"></span>
                    </div>
                    <div class="self-end pb-1.5 text-zinc-500">
                        <span x-text="interval === 'month' ? 'per month' : 'per year'"></span>
                    </div>
                </div>
            @endif

            {{-- Savings note --}}
            <div
                x-show="interval === 'year'"
                x-transition
                class="pt-1 text-sm text-emerald-600 dark:text-emerald-400"
            >
                @if($isEapCustomer)
                    Save ${{ $eapSavingsVsMonthly }}/year (compared to monthly pricing) with your Early Access discount
                @else
                    Save $70/year vs monthly
                @endif
            </div>

            {{-- CTA Button --}}
            @auth
                @if($isAlreadyUltra)
                    <div class="my-5 block w-full rounded-2xl bg-emerald-100 py-4 text-center text-sm font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                        You're on Ultra
                    </div>
                @elseif($hasExistingSubscription)
                    <button
                        type="button"
                        @click="showUpgradeModal = true"
                        wire:click="previewUpgrade"
                        class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                        aria-label="Upgrade to Ultra plan"
                    >
                        Upgrade to Ultra
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="createCheckoutSession('max')"
                        class="my-5 block w-full rounded-2xl bg-zinc-800 py-4 text-center text-sm font-medium text-white transition duration-200 ease-in-out hover:bg-zinc-700 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                        aria-label="Get started with Ultra plan"
                    >
                        Get started
                    </button>
                @endif
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
                    <div class="font-medium">All first-party plugins at no extra cost</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Keep up to 90% of Marketplace plugin earnings</div>
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
                    <div class="font-medium">Exclusive discounts on future NativePHP products</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Premium support &mdash; private channels, expedited turnaround</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">Teams &mdash; invite your whole team to share your Ultra benefits</div>
                </div>
                <div class="flex items-center gap-2">
                    <div
                        class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
                        aria-hidden="true"
                    >
                        <x-icons.checkmark class="size-5 shrink-0" />
                    </div>
                    <div class="font-medium">{{ config('subscriptions.plans.max.included_seats') }} seats included <span x-text="interval === 'month' ? '(${{ config('subscriptions.plans.max.extra_seat_price_monthly') }}/mo per extra seat)' : '(${{ config('subscriptions.plans.max.extra_seat_price_yearly') }}/mo per extra seat)'"></span></div>
                </div>
            </div>
        </div>

        {{-- Upgrade Confirmation Modal --}}
        @auth
            @if($hasExistingSubscription && !$isAlreadyUltra)
                <template x-teleport="body">
                    <div
                        x-show="showUpgradeModal"
                        x-transition.opacity
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                        @keydown.escape.window="showUpgradeModal = false"
                    >
                        <div
                            x-show="showUpgradeModal"
                            x-transition
                            @click.outside="showUpgradeModal = false"
                            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-zinc-900"
                        >
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Upgrade to Ultra</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                You're upgrading from <strong>{{ $currentPlanName }}</strong> to <strong>Ultra</strong>.
                            </p>

                            {{-- Interval Toggle --}}
                            <div class="mt-4">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Billing interval</label>
                                <div class="mt-2 inline-flex items-center gap-1 rounded-full bg-gray-100 p-1 dark:bg-zinc-800" role="radiogroup" aria-label="Upgrade billing interval">
                                    <button
                                        type="button"
                                        @click="interval = 'month'; $wire.previewUpgrade()"
                                        :class="interval === 'month'
                                            ? 'bg-white text-black shadow-sm dark:bg-zinc-600 dark:text-white'
                                            : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                        class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                                        role="radio"
                                        :aria-checked="interval === 'month'"
                                    >
                                        Monthly
                                    </button>
                                    <button
                                        type="button"
                                        @click="interval = 'year'; $wire.previewUpgrade()"
                                        :class="interval === 'year'
                                            ? 'bg-white text-black shadow-sm dark:bg-zinc-600 dark:text-white'
                                            : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                                        class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                                        role="radio"
                                        :aria-checked="interval === 'year'"
                                    >
                                        Yearly
                                    </button>
                                </div>
                            </div>

                            {{-- Proration Preview --}}
                            <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-zinc-800">
                                {{-- Loading State --}}
                                <div wire:loading wire:target="previewUpgrade" class="space-y-2">
                                    <div class="h-4 w-3/4 animate-pulse rounded bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="h-4 w-1/2 animate-pulse rounded bg-gray-200 dark:bg-zinc-700"></div>
                                    <div class="mt-3 h-6 w-2/3 animate-pulse rounded bg-gray-200 dark:bg-zinc-700"></div>
                                </div>

                                {{-- Preview Data --}}
                                <div wire:loading.remove wire:target="previewUpgrade">
                                    @if($upgradePreview)
                                        <div class="space-y-2 text-sm">
                                            <div class="flex items-baseline justify-between">
                                                <span class="text-gray-600 dark:text-gray-400">New plan (Ultra)@if($upgradePreview['is_prorated']) <span class="text-gray-400 dark:text-gray-500">(pro-rated)</span>@endif</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $upgradePreview['new_charge'] }}</span>
                                            </div>
                                            @if($upgradePreview['credit'])
                                                <div class="flex items-baseline justify-between">
                                                    <span class="text-gray-600 dark:text-gray-400">Credit for unused {{ $currentPlanName }} time</span>
                                                    <span class="font-medium text-emerald-600 dark:text-emerald-400">-{{ $upgradePreview['credit'] }}</span>
                                                </div>
                                            @endif
                                            <div class="border-t border-gray-200 pt-2 dark:border-zinc-700">
                                                <div class="flex items-baseline justify-between">
                                                    <span class="font-medium text-gray-900 dark:text-white">Due today</span>
                                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ $upgradePreview['amount_due'] }}</span>
                                                </div>
                                                @if($upgradePreview['remaining_credit'])
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $upgradePreview['remaining_credit'] }} will be credited to your next invoice.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Unable to load pricing preview. You can still proceed with the upgrade.
                                        </p>
                                    @endif
                                </div>
                            </div>

                            @if($isEapCustomer)
                                <div
                                    x-show="interval === 'year'"
                                    x-transition
                                    class="mt-2 flex items-center gap-2 text-xs"
                                >
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">EAP discount applied</span>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="mt-6 flex gap-3">
                                <button
                                    type="button"
                                    @click="showUpgradeModal = false"
                                    class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-800"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    wire:click="upgradeSubscription"
                                    wire:loading.attr="disabled"
                                    wire:target="upgradeSubscription, previewUpgrade"
                                    class="flex-1 rounded-xl bg-zinc-800 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-zinc-700 disabled:opacity-50 dark:bg-white dark:text-black dark:hover:bg-zinc-200"
                                >
                                    <span wire:loading.remove wire:target="upgradeSubscription">Confirm upgrade</span>
                                    <span wire:loading wire:target="upgradeSubscription">Upgrading...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            @endif
        @endauth
    </div>

    @guest
        <livewire:purchase-modal />
    @endguest
</section>
