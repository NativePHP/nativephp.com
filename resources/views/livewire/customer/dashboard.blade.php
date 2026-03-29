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
