<div>
    <div class="mb-6">
        <flux:heading size="xl">Become a Plugin Developer</flux:heading>
        <flux:text>Set up your account to sell plugins on NativePHP</flux:text>
    </div>

    {{-- Session Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('message'))
        <flux:callout variant="secondary" icon="information-circle" class="mb-6">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="mx-auto max-w-3xl space-y-8">
        {{-- Hero Card --}}
        <flux:card>
            <div class="p-4 text-center">
                <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                    <x-heroicon-o-banknotes class="size-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <flux:heading size="lg" class="mt-4">
                    @if ($this->hasExistingAccount)
                        Complete Your Onboarding
                    @else
                        Start Selling Plugins
                    @endif
                </flux:heading>
                <flux:text class="mt-2">
                    @if ($this->hasExistingAccount)
                        You've started the onboarding process. Complete the remaining steps to start receiving payouts.
                    @else
                        Connect your Stripe account to receive payments when users purchase your plugins.
                    @endif
                </flux:text>
            </div>

            {{-- Benefits --}}
            <div class="mt-6 rounded-lg bg-gray-50 p-6 dark:bg-gray-700/50">
                <flux:heading>Why sell on NativePHP?</flux:heading>
                <ul class="mt-4 space-y-3">
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500" />
                        <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">70% Revenue Share</strong> — You keep the majority of every sale</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500" />
                        <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Built-in Distribution</strong> — Automatic Composer repository hosting</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500" />
                        <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Targeted Audience</strong> — Reach NativePHP developers directly</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-emerald-500" />
                        <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Automatic Payouts</strong> — Get paid directly to your bank account</span>
                    </li>
                </ul>
            </div>

            {{-- Status for existing account --}}
            @if ($this->hasExistingAccount && $this->developerAccount)
                <flux:callout variant="warning" icon="exclamation-triangle" class="mt-6">
                    <flux:callout.heading>Onboarding Incomplete</flux:callout.heading>
                    <flux:callout.text>Your Stripe account requires additional information before you can receive payouts.</flux:callout.text>
                </flux:callout>
            @endif

            {{-- CTA Button --}}
            <div class="mt-6">
                <form action="{{ route('customer.developer.onboarding.start') }}" method="POST">
                    @csrf
                    <flux:button type="submit" variant="primary" class="w-full">
                        @if ($this->hasExistingAccount)
                            Continue Onboarding
                        @else
                            Connect with Stripe
                        @endif
                    </flux:button>
                </form>
            </div>

            <flux:text class="mt-4 text-center text-xs">
                You'll be redirected to Stripe to complete the onboarding process securely.
            </flux:text>
        </flux:card>

        {{-- FAQ --}}
        <flux:card>
            <flux:heading size="lg">Frequently Asked Questions</flux:heading>

            <div class="mt-4 space-y-4">
                <div>
                    <flux:heading>How does the revenue share work?</flux:heading>
                    <flux:text class="mt-1">
                        You receive 70% of each sale. NativePHP retains 30% to cover payment processing, hosting, and platform maintenance.
                    </flux:text>
                </div>

                <div>
                    <flux:heading>When do I get paid?</flux:heading>
                    <flux:text class="mt-1">
                        Payouts are processed automatically through Stripe Connect. Funds are typically available within 2–7 business days after a sale.
                    </flux:text>
                </div>

                <div>
                    <flux:heading>What do I need to get started?</flux:heading>
                    <flux:text class="mt-1">
                        You'll need a Stripe account (or create one during onboarding), a GitHub repository for your plugin, and a nativephp.json configuration file.
                    </flux:text>
                </div>
            </div>
        </flux:card>
    </div>
</div>
