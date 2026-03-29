<div>
    <div class="mb-6">
        <flux:heading size="xl">Dashboard</flux:heading>
        <flux:text>Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}</flux:text>
    </div>

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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="font-medium text-zinc-900 dark:text-zinc-100">
                        Upgrade to NativePHP Ultra
                    </h3>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                        Get all first-party plugins for free, premium support, team management, and more.
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
            @if(auth()->user()->shouldSeeFreePluginsOffer())
                <x-free-plugins-offer-banner :inline="true" />
            @endif
        @else
            <x-discounts-banner :inline="true" />
        @endfeature
    </div>

    {{-- Dashboard Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Licenses Card --}}
        <x-dashboard-card
            title="Licenses"
            :count="$this->licenseCount"
            icon="key"
            color="blue"
            :href="route('customer.licenses.list')"
            link-text="View licenses"
        />

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
        @if($this->activeSubscription)
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
                    description="Invite up to 4 members to share Ultra benefits (5 seats total)"
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
