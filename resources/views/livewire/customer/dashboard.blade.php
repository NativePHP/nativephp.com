<div>
    <div class="mb-6">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text>Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}</flux:text>
    </div>

    {{-- Email Verification Banner --}}
    @if (!auth()->user()->hasVerifiedEmail())
        <flux:callout variant="warning" icon="envelope" class="mb-6">
            <flux:callout.heading>Please verify your email address.</flux:callout.heading>
            <flux:callout.text>
                We sent a verification email when you registered. Click the link in that email to verify your account.

                @if (session('status'))
                    <span class="font-medium">{{ session('status') }}</span>
                @endif
            </flux:callout.text>
            <x-slot:actions>
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <flux:button type="submit" variant="filled" size="sm">Resend verification email</flux:button>
                </form>
            </x-slot:actions>
        </flux:callout>
    @endif

    {{-- Session Messages --}}
    @if (session('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('message'))
        <flux:callout variant="secondary" icon="information-circle" class="mb-6">
            <flux:callout.text>{{ session('message') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if (session('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Ultra Upsell Banner --}}
    @if(!$this->hasUltraSubscription)
        <div class="mb-6 rounded-lg border border-zinc-300 bg-gradient-to-r from-zinc-100 to-zinc-200 p-6 dark:border-zinc-600 dark:from-zinc-800 dark:to-zinc-900">
            <div class="flex items-start">
                <div class="shrink-0 text-zinc-700 dark:text-zinc-300">
                    <x-heroicon-s-bolt class="size-6" />
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="font-medium text-zinc-900 dark:text-zinc-100">
                        Upgrade to NativePHP Ultra
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Access all first-party plugins at no extra cost, premium support, team management, and more.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('pricing') }}" class="inline-flex items-center rounded-md border border-transparent bg-black px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:bg-white dark:text-black dark:hover:bg-zinc-200">
                            Learn more
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Banners --}}
    <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        @feature(App\Features\ShowPlugins::class)
            @if(auth()->user()->shouldSeeFreePluginsOffer() && !$this->hasUltraSubscription)
                <x-free-plugins-offer-banner :inline="true" />
            @endif
        @else
            <x-discounts-banner :inline="true" />
        @endfeature
    </div>

    {{-- Dashboard Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Licenses Card --}}
        @if($this->licenseCount > 0)
            <x-dashboard-card
                title="Licenses"
                :count="$this->licenseCount"
                icon="key"
                color="blue"
                :href="route('customer.licenses.list')"
                link-text="View licenses"
            />
        @endif

        {{-- EAP Status Card --}}
        <x-dashboard-card
            title="Early Access Program"
            :value="$this->isEapCustomer ? 'Member' : 'Not a member'"
            :badge="$this->isEapCustomer ? 'EAP' : null"
            :badge-color="$this->isEapCustomer ? 'green' : 'gray'"
            icon="star"
            :color="$this->isEapCustomer ? 'yellow' : 'gray'"
            :description="$this->isEapCustomer ? 'You purchased before June 2025' : null"
        />

        {{-- Subscription Card --}}
        @if($this->activeSubscription?->active())
            <x-dashboard-card
                title="Subscription"
                :value="$this->subscriptionName"
                badge="Active"
                badge-color="green"
                icon="credit-card"
                color="green"
                :href="route('customer.billing-portal')"
                link-text="Manage subscription"
            />
        @else
            <x-dashboard-card
                title="Subscription"
                value="No active subscription"
                icon="credit-card"
                color="gray"
                :href="$this->renewalLicenseKey ? route('license.renewal', $this->renewalLicenseKey) : route('pricing')"
                :link-text="$this->renewalLicenseKey ? 'Renew license' : 'View plans'"
            />
        @endif

        {{-- Team Card --}}
        @if($this->hasUltraSubscription)
            @if($this->ownedTeam)
                <x-dashboard-card
                    title="Team"
                    :value="$this->ownedTeam->name"
                    icon="user-group"
                    color="purple"
                    :href="route('customer.team.index')"
                    link-text="Manage members"
                    :description="$this->teamMemberCount === 1 ? '1 active member' : $this->teamMemberCount . ' active members'"
                />
            @else
                <x-dashboard-card
                    title="Team"
                    value="No team yet"
                    icon="user-group"
                    color="gray"
                    :href="route('customer.team.index')"
                    link-text="Create a team"
                    description="Invite up to {{ config('subscriptions.plans.max.included_seats') - 1 }} members to share Ultra benefits ({{ config('subscriptions.plans.max.included_seats') }} seats total)"
                />
            @endif
        @endif

        {{-- Premium Plugins Card --}}
        @feature(App\Features\ShowPlugins::class)
            <x-dashboard-card
                title="Premium Plugins"
                :count="$this->pluginLicenseCount"
                icon="puzzle-piece"
                color="purple"
                :href="route('customer.purchased-plugins.index')"
                link-text="View plugins"
            />

            {{-- Submit Plugin Card --}}
            <x-dashboard-card
                title="Plugin Marketplace"
                icon="code-bracket"
                color="indigo"
                description="Submit a plugin to the NativePHP marketplace"
                :href="route('customer.plugins.create')"
                link-text="Submit a plugin"
            />
        @endfeature

        {{-- Connected Accounts Card --}}
        <x-dashboard-card
            title="Connected Accounts"
            :value="$this->connectedAccountsCount . ' of 2'"
            icon="link"
            color="indigo"
            :href="route('customer.integrations')"
            link-text="Manage integrations"
            :description="$this->connectedAccountsDescription"
        />

        {{-- Purchase History Card --}}
        <x-dashboard-card
            title="Purchase History"
            :count="$this->totalPurchases"
            icon="receipt-refund"
            color="gray"
            :href="route('customer.purchase-history.index')"
            link-text="View history"
        />
    </div>

</div>
