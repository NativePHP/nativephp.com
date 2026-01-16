<x-layout title="Your Licenses">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Your Licenses</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage your NativePHP licenses
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('customer.showcase.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Showcase
                        </a>
                        @feature(App\Features\ShowPlugins::class)
                            <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <x-icons.puzzle class="mr-2 -ml-1 size-4" />
                                Plugins
                            </a>
                        @endfeature
                        <a href="{{ route('customer.integrations') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Integrations
                        </a>
                        <a href="{{ route('customer.billing-portal') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Manage Subscription
                        </a>
                        <form method="POST" action="{{ route('customer.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Log out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Banners --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if(auth()->user()->hasActualLicense())
                    <x-discounts-banner :inline="true" />
                @endif
                <livewire:wall-of-love-banner :inline="true" />
            </div>
        </div>

        {{-- Purchased Plugins --}}
        @feature(App\Features\ShowPlugins::class)
            @if($pluginLicenses->count() > 0)
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Purchased Plugins</h2>
                        <a href="{{ route('plugins.directory') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            Browse more plugins
                        </a>
                    </div>

                    {{-- Plugin License Key --}}
                    @if(auth()->user()->plugin_license_key)
                        <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-900/20">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-indigo-900 dark:text-indigo-200">Your Plugin License Key</h3>
                                    <p class="mt-1 text-xs text-indigo-700 dark:text-indigo-300">
                                        Use this key with your email to authenticate Composer for paid plugins.
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    onclick="navigator.clipboard.writeText('{{ auth()->user()->plugin_license_key }}'); this.textContent = 'Copied!'; setTimeout(() => this.textContent = 'Copy', 2000);"
                                    class="rounded bg-indigo-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-indigo-700"
                                >
                                    Copy
                                </button>
                            </div>
                            <div class="mt-3">
                                <code class="block rounded bg-indigo-100 px-3 py-2 font-mono text-xs text-indigo-900 dark:bg-indigo-900/50 dark:text-indigo-200 break-all">
                                    {{ auth()->user()->plugin_license_key }}
                                </code>
                            </div>
                            <details class="mt-3">
                                <summary class="cursor-pointer text-xs font-medium text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-indigo-100">
                                    How to configure Composer
                                </summary>
                                <div class="mt-2 rounded bg-gray-900 p-3">
                                    <code class="block font-mono text-xs text-gray-100 whitespace-pre">composer config http-basic.plugins.nativephp.com {{ auth()->user()->email }} {{ auth()->user()->plugin_license_key }}</code>
                                </div>
                            </details>
                        </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pluginLicenses as $pluginLicense)
                                <li>
                                    <a href="{{ route('plugins.show', $pluginLicense->plugin->slug ?? $pluginLicense->plugin->id) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        @if($pluginLicense->plugin->hasLogo())
                                                            <img src="{{ $pluginLicense->plugin->getLogoUrl() }}" alt="{{ $pluginLicense->plugin->name }}" class="size-10 rounded-lg object-cover">
                                                        @else
                                                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                                                <x-icons.puzzle class="size-5" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                            {{ $pluginLicense->plugin->name }}
                                                        </p>
                                                        @if($pluginLicense->plugin->description)
                                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-1">
                                                                {{ $pluginLicense->plugin->description }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="flex flex-col items-end">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                        Licensed
                                                    </span>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        Purchased {{ $pluginLicense->purchased_at->format('M j, Y') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endfeature

        {{-- Content --}}
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">NativePHP Licenses</h2>
            @if($licenses->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($licenses as $license)
                            @php
                                $isLegacyLicense = $license->isLegacy();
                                $daysUntilExpiry = $license->expires_at ? $license->expires_at->diffInDays(now()) : null;
                                $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null && $daysUntilExpiry <= 30 && !$license->expires_at->isPast();
                            @endphp
                            <li class="{{ $needsRenewal ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                <a href="{{ route('customer.licenses.show', $license->key) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700 {{ $needsRenewal ? 'hover:bg-blue-100 dark:hover:bg-blue-900/30' : '' }}">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if($license->is_suspended)
                                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                                    @elseif($license->expires_at && $license->expires_at->isPast())
                                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                                    @elseif($needsRenewal)
                                                        <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                                                    @else
                                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-start">
                                                        <div class="flex flex-col">
                                                            @if($license->name)
                                                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">
                                                                    {{ $license->name }}
                                                                </p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $license->policy_name }}
                                                                </p>
                                                            @else
                                                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">
                                                                    {{ $license->policy_name }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        @if($license->is_suspended)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Suspended
                                                            </span>
                                                        @elseif($license->expires_at && $license->expires_at->isPast())
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Expired
                                                            </span>
                                                        @elseif($needsRenewal)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                                Needs Renewal
                                                            </span>
                                                        @else
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                        {{ $license->key }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                @if($needsRenewal)
                                                    <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                        Expires in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry === 1 ? '' : 's' }}
                                                    </p>

                                                    @if($isLegacyLicense)
                                                        <p class="text-xs text-blue-500 dark:text-blue-300">
                                                            Lock in Early Access Pricing
                                                        </p>
                                                    @endif
                                                @else
                                                    <p class="text-sm text-gray-900 dark:text-white">
                                                        @if($license->expires_at)
                                                            Expires {{ $license->expires_at->format('M j, Y') }}
                                                        @else
                                                            No expiration
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Created {{ $license->created_at->format('M j, Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No licenses found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            You don't have any licenses yet. If you believe this is an error, please contact support.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
