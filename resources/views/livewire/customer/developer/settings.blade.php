<div class="mx-auto max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">Developer Settings</flux:heading>
        <flux:text>Manage your developer profile and account status.</flux:text>
    </div>

    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Author Display Name --}}
    <flux:card class="mb-6">
        <form wire:submit="updateDisplayName">
            <flux:heading size="lg">Author Display Name</flux:heading>
            <flux:text class="mb-4">This is how your name will appear on your plugins in the directory.</flux:text>

            <div class="mb-4">
                <flux:input
                    wire:model="displayName"
                    label="Display Name"
                    placeholder="{{ auth()->user()->name }}"
                />
            </div>

            <flux:text class="mb-4 text-xs">
                Leave blank to use your account name: <span class="font-medium">{{ auth()->user()->name }}</span>
            </flux:text>

            <flux:button type="submit" variant="primary">Save</flux:button>
        </form>
    </flux:card>

    {{-- Stripe Account Status --}}
    @feature(App\Features\AllowPaidPlugins::class)
        <flux:card>
            <flux:heading size="lg">Stripe Account Status</flux:heading>
            <flux:separator class="my-4" />

            @if ($this->developerAccount && $this->developerAccount->hasCompletedOnboarding())
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if ($this->developerAccount->canReceivePayouts())
                            <div class="flex size-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                                <x-heroicon-s-check class="size-5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Account Active</p>
                                <flux:text>Your account is fully set up to receive payouts</flux:text>
                            </div>
                        @else
                            <div class="flex size-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                                <x-heroicon-s-exclamation-triangle class="size-5 text-yellow-600 dark:text-yellow-400" />
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Action Required</p>
                                <flux:text>Additional information needed for payouts</flux:text>
                            </div>
                        @endif
                    </div>
                    @if (! $this->developerAccount->canReceivePayouts())
                        <flux:button variant="primary" href="{{ route('customer.developer.onboarding') }}">
                            Complete Setup
                        </flux:button>
                    @endif
                </div>
            @elseif ($this->developerAccount)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                            <x-heroicon-s-exclamation-triangle class="size-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Setup Incomplete</p>
                            <flux:text>You've started the Stripe Connect setup but there are still some steps remaining.</flux:text>
                        </div>
                    </div>
                    <flux:button variant="primary" href="{{ route('customer.developer.onboarding') }}">
                        Continue Setup
                    </flux:button>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex size-10 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                            <x-heroicon-s-credit-card class="size-5 text-gray-500 dark:text-gray-400" />
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Not Connected</p>
                            <flux:text>Connect your Stripe account to receive payouts for paid plugin sales.</flux:text>
                        </div>
                    </div>
                    <flux:button variant="primary" href="{{ route('customer.developer.onboarding') }}">
                        Connect Stripe
                    </flux:button>
                </div>
            @endif
        </flux:card>
    @endfeature
</div>
