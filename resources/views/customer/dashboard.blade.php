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

        {{-- Ultra Upsell --}}
        @if($showUltraUpsell)
            <div class="mx-auto mb-6 max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative overflow-hidden rounded-lg border border-indigo-200 bg-gradient-to-r from-indigo-50 to-purple-50 p-6 shadow-sm dark:border-indigo-800 dark:from-indigo-950/50 dark:to-purple-950/50">
                    <div class="absolute -right-6 -top-6 size-32 rounded-full bg-indigo-500/10 dark:bg-indigo-400/10"></div>
                    <div class="relative flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-indigo-900 dark:text-indigo-100">Upgrade to Ultra</h3>
                            <p class="mt-1 text-sm text-indigo-700 dark:text-indigo-300">
                                Get access to all official plugins, team sharing, and more with an Ultra subscription.
                            </p>
                        </div>
                        <a href="{{ route('pricing') }}" class="inline-flex shrink-0 items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            Learn more
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endif

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

                {{-- Team Card --}}
                @if($hasTeam)
                    <x-dashboard-card
                        title="Team"
                        :value="$teamName"
                        :badge="$teamMemberCount . ' members'"
                        badge-color="blue"
                        :second-badge="$teamPendingCount > 0 ? $teamPendingCount . ' pending' : null"
                        second-badge-color="yellow"
                        icon="user-group"
                        color="teal"
                        :href="route('customer.team.index')"
                        link-text="Manage team"
                    />
                @elseif($hasMaxAccess)
                    <x-dashboard-card
                        title="Team"
                        value="No team yet"
                        icon="user-group"
                        color="gray"
                        :href="route('customer.team.index')"
                        link-text="Create a team"
                        description="Share your Ultra benefits with your team"
                    />
                @endif

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

    </div>
</x-layout>
