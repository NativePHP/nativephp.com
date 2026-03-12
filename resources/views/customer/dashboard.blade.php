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

        {{-- Updated Terms Banner --}}
        @if ($developerAccount && $developerAccount->hasAcceptedPluginTerms() && !$developerAccount->hasAcceptedCurrentTerms())
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-900/20">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <svg class="size-5 shrink-0 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                Our <a href="{{ route('developer-terms') }}" class="underline" target="_blank">Plugin Developer Terms and Conditions</a> have been updated. You must accept the new terms before you can submit new plugins.
                            </p>
                        </div>
                        <a href="{{ route('customer.developer.onboarding') }}" class="shrink-0 rounded-md bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-500">
                            Review &amp; Accept
                        </a>
                    </div>
                </div>
            </div>
        @endif

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
