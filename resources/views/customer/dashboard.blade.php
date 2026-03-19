<x-layout title="Dashboard">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Banners --}}
        <div class="mx-auto mb-6 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                @feature(App\Features\ShowPlugins::class)
                    @if(auth()->user()->shouldSeeFreePluginsOffer())
                        <x-free-plugins-offer-banner :inline="true" />
                    @endif
                @else
                    <x-discounts-banner :inline="true" />
                @endfeature
                <livewire:wall-of-love-banner :inline="true" />
            </div>
        </div>

        {{-- Dashboard Cards --}}
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {{-- Licenses Card --}}
                <x-dashboard-card
                    title="Licenses"
                    :count="$licenseCount"
                    icon="key"
                    color="blue"
                    :href="route('customer.licenses.list')"
                    link-text="View licenses"
                />

                {{-- EAP Status Card --}}
                <x-dashboard-card
                    title="Early Access Program"
                    :value="$isEapCustomer ? 'Member' : 'Not a member'"
                    :badge="$isEapCustomer ? 'EAP' : null"
                    :badge-color="$isEapCustomer ? 'green' : 'gray'"
                    icon="star"
                    :color="$isEapCustomer ? 'yellow' : 'gray'"
                    :description="$isEapCustomer ? 'You purchased before June 2025' : null"
                />

                {{-- Subscription Card --}}
                @if($activeSubscription)
                    <x-dashboard-card
                        title="Subscription"
                        :value="$subscriptionName"
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
                        :href="$renewalLicenseKey ? route('license.renewal', $renewalLicenseKey) : route('pricing')"
                        :link-text="$renewalLicenseKey ? 'Renew license' : 'View plans'"
                    />
                @endif

                {{-- Premium Plugins Card --}}
                @feature(App\Features\ShowPlugins::class)
                    <x-dashboard-card
                        title="Premium Plugins"
                        :count="$pluginLicenseCount"
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
                    :value="$connectedAccountsCount . ' of 2'"
                    icon="link"
                    color="indigo"
                    :href="route('customer.integrations')"
                    link-text="Manage integrations"
                    :description="$connectedAccountsDescription"
                />

                {{-- Purchase History Card --}}
                <x-dashboard-card
                    title="Purchase History"
                    :count="$totalPurchases"
                    icon="receipt-refund"
                    color="gray"
                    :href="route('customer.purchase-history.index')"
                    link-text="View history"
                />
            </div>
        </div>

        {{-- Session Messages --}}
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-800 dark:bg-emerald-900/20">
                    <p class="text-sm text-emerald-800 dark:text-emerald-200">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('message'))
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('message') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                    <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layout>
