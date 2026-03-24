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
        {{-- Status for existing account --}}
        @if ($this->hasExistingAccount && $this->developerAccount)
            <flux:callout variant="warning" icon="exclamation-triangle">
                <flux:callout.heading>Onboarding Incomplete</flux:callout.heading>
                <flux:callout.text>Your Stripe account requires additional information before you can receive payouts.</flux:callout.text>
            </flux:callout>
        @endif

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
                    Connect your Stripe account to receive payments when users purchase your plugins.
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

            {{-- Country & Currency Selection --}}
            <div class="mt-6 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-700/50">
                <flux:heading>Your Country</flux:heading>
                <flux:text class="mt-2">
                    Select the country where your bank account is located. This determines which currencies are available for payouts.
                </flux:text>

                <div class="mt-4 space-y-4">
                    <div>
                        <flux:select wire:model.live="country" variant="listbox" searchable label="Country" placeholder="Select your country...">
                            @foreach ($this->countries as $code => $details)
                                <flux:select.option value="{{ $code }}">
                                    {{ $details['flag'] }} {{ $details['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('country')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>

                    @if (count($this->availableCurrencies) > 0)
                        <div>
                            <flux:select wire:model="payoutCurrency" label="Payout Currency">
                                @foreach ($this->availableCurrencies as $code => $name)
                                    <flux:select.option value="{{ $code }}">
                                        {{ $name }} ({{ $code }})
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                            @error('payout_currency')
                                <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            {{-- Developer Terms Agreement & CTA Button --}}
            <div class="mt-6">
                <form action="{{ route('customer.developer.onboarding.start') }}" method="POST" x-data="{ termsAccepted: {{ ($this->developerAccount?->hasAcceptedCurrentTerms()) ? 'true' : 'false' }} }">
                    @csrf

                    <input type="hidden" name="country" value="{{ $this->country }}" />
                    <input type="hidden" name="payout_currency" value="{{ $this->payoutCurrency }}" />

                    @if ($this->developerAccount?->hasAcceptedCurrentTerms())
                        <input type="hidden" name="accepted_plugin_terms" value="1" />

                        <flux:callout variant="success" icon="check-circle" class="mb-6">
                            <flux:callout.text>
                                You accepted the <a href="{{ route('developer-terms') }}" class="font-medium underline" target="_blank">Plugin Developer Terms and Conditions</a> on {{ $this->developerAccount->accepted_plugin_terms_at->format('F j, Y') }}.
                            </flux:callout.text>
                        </flux:callout>
                    @else
                        <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-700/50">
                            <flux:heading>Plugin Developer Terms and Conditions</flux:heading>
                            <flux:text class="mt-2">
                                Before you can sell plugins on the Marketplace, you must agree to the following key terms:
                            </flux:text>

                            <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                                <li class="flex items-start gap-3">
                                    <x-heroicon-o-currency-dollar class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                    <span><strong class="text-gray-900 dark:text-white">30% Platform Fee</strong> &mdash; NativePHP retains 30% of each sale to cover payment processing, hosting, and platform maintenance</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <x-heroicon-o-shield-check class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                    <span><strong class="text-gray-900 dark:text-white">Your Responsibility</strong> &mdash; You are solely responsible for your plugin's quality, performance, and customer support</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <x-heroicon-o-adjustments-horizontal class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                    <span><strong class="text-gray-900 dark:text-white">Listing Criteria</strong> &mdash; NativePHP sets and may change listing standards at any time, and may remove plugins at its discretion</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <x-heroicon-o-tag class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                    <span><strong class="text-gray-900 dark:text-white">Pricing & Discounts</strong> &mdash; NativePHP sets plugin prices and may offer discounts at its discretion</span>
                                </li>
                            </ul>

                            <div class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-600">
                                <label class="flex cursor-pointer items-start gap-3">
                                    <input
                                        type="checkbox"
                                        name="accepted_plugin_terms"
                                        value="1"
                                        x-model="termsAccepted"
                                        class="mt-0.5 size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700"
                                    />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        I have read and agree to the
                                        <a href="{{ route('developer-terms') }}" class="font-medium text-indigo-600 underline hover:text-indigo-500 dark:text-indigo-400" target="_blank">Plugin Developer Terms and Conditions</a>
                                    </span>
                                </label>
                                @error('accepted_plugin_terms')
                                    <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <flux:button type="submit" variant="primary" class="w-full" x-bind:disabled="!termsAccepted || !$wire.country">
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
