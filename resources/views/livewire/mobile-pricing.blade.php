<section
    id="pricing"
    class="mx-auto mt-24 max-w-6xl"
    aria-labelledby="pricing-heading"
>
    @if (! $discounted)
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
    @endif

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
        class="{{ $discounted ? 'grid-cols-1 md:grid-cols-2' : 'grid-cols-[repeat(auto-fill,minmax(19rem,1fr))]' }} mt-10 grid items-start gap-x-6 gap-y-8"
        aria-label="Pricing plans"
    >
        @if (! $discounted)
            {{-- Mini Plan --}}
            <div
                class="h-full rounded-2xl bg-gray-100 p-7 opacity-0 dark:bg-mirage"
                aria-labelledby="pro-plan-heading"
            >
                {{-- Plan Name --}}
                <h3
                    id="pro-plan-heading"
                    class="text-3xl font-semibold"
                >
                    <span
                        class="rounded-full bg-zinc-300 px-4 dark:bg-zinc-600"
                    >
                        Mini
                    </span>
                </h3>

                {{-- Price --}}
                <div
                    class="flex items-start gap-1.5 pt-5"
                    aria-label="Price: FREE (with Bifrost subscription)"
                >
                    <div class="text-5xl font-semibold">FREE</div>
                    <div class="self-end pb-1.5 text-zinc-500">
                        with
                        <a
                            href="/bifrost"
                            target="_blank"
                            class="underline underline-offset-2 transition-colors hover:text-zinc-400 dark:text-zinc-300 dark:hover:text-zinc-100"
                        >
                            Bifrost
                        </a>
                    </div>
                </div>

                <a
                    href="https://bifrost.nativephp.com/register"
                    class="my-5 block w-full rounded-2xl bg-zinc-200 py-4 text-center text-sm font-medium transition duration-200 ease-in-out hover:bg-zinc-800 hover:text-white dark:bg-slate-700/30 dark:hover:bg-slate-700/40"
                    aria-label="Get started with a FREE Mini license"
                >
                    Get started
                </a>

                <x-pricing-plan-features plan-name="Mini" />
            </div>
        @endif

        {{-- Pro Plan --}}
        <x-pricing-plan
            name="Pro"
            id="pro"
            :features="[
                'apps' => 'unlimited',
                'keys' => 10,
            ]"
            price="200"
            discounted_price="150"
            :$discounted
        />

        {{-- Max Plan - Most Popular --}}
        <x-pricing-plan
            name="Max"
            id="max"
            :features="[
                'apps' => 'unlimited',
                'keys' => 'Unlimited',
                'discord' => true,
                'github' => true,
                'priority' => true,
                'support' => true,
            ]"
            price="350"
            discounted_price="250"
            :$discounted
            :popular="true"
        />
    </div>

    @guest
        <livewire:purchase-modal />
    @endguest
</section>
