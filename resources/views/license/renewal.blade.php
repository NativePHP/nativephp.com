<x-layouts.dashboard title="Upgrade to Ultra">
    <div class="max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl">Upgrade to Ultra</flux:heading>
            <flux:text>Your Early Access license qualifies you for special upgrade pricing.</flux:text>
        </div>

        <flux:callout variant="info" icon="star" class="mb-6">
            <flux:callout.heading>Early Access Pricing</flux:callout.heading>
            <flux:callout.text>
                As an early adopter, you can upgrade to Ultra at a special discounted rate.
                This pricing is only available until your license expires. After that you will have to renew at full price.
            </flux:callout.text>
        </flux:callout>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            {{-- Yearly Option --}}
            <flux:card>
                <div class="flex flex-col items-center text-center">
                    <flux:badge color="green" class="mb-3">Recommended</flux:badge>
                    <flux:heading size="lg">Yearly</flux:heading>
                    <div class="mt-2 mb-1">
                        <span class="text-3xl font-bold text-zinc-900 dark:text-white">${{ config('subscriptions.plans.max.eap_price_yearly') }}</span>
                        <flux:text class="inline">/year</flux:text>
                    </div>
                    <flux:text class="mb-4 text-xs">Early Access Price</flux:text>

                    <form method="POST" action="{{ route('license.renewal.checkout', $license->key) }}" class="w-full">
                        @csrf
                        <input type="hidden" name="billing_period" value="yearly">
                        <flux:button type="submit" variant="primary" class="w-full">
                            Upgrade Yearly
                        </flux:button>
                    </form>
                </div>
            </flux:card>

            {{-- Monthly Option --}}
            <flux:card>
                <div class="flex flex-col items-center text-center">
                    <div class="mb-3 h-5"></div>
                    <flux:heading size="lg">Monthly</flux:heading>
                    <div class="mt-2 mb-1">
                        <span class="text-3xl font-bold text-zinc-900 dark:text-white">${{ config('subscriptions.plans.max.price_monthly') }}</span>
                        <flux:text class="inline">/month</flux:text>
                    </div>
                    <flux:text class="mb-4 text-xs">Billed monthly</flux:text>

                    <form method="POST" action="{{ route('license.renewal.checkout', $license->key) }}" class="w-full">
                        @csrf
                        <input type="hidden" name="billing_period" value="monthly">
                        <flux:button type="submit" variant="ghost" class="w-full">
                            Upgrade Monthly
                        </flux:button>
                    </form>
                </div>
            </flux:card>
        </div>

        <flux:text class="mt-4 text-center text-xs">
            You'll be redirected to Stripe to complete your subscription setup.
        </flux:text>
    </div>
</x-layouts.dashboard>
