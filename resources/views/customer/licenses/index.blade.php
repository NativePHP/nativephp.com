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
                        <a href="{{ route('customer.integrations') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Integrations
                        </a>
                        <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <x-icons.puzzle class="mr-2 -ml-1 size-4" />
                            Plugins
                        </a>
                        <a href="{{ route('customer.billing-portal') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Manage Subscription
                        </a>
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

        {{-- Content --}}
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session()->has('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

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
            @endif

            {{-- Assigned Sub-Licenses --}}
            @if($assignedSubLicenses->count() > 0)
                <div class="mt-8">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Assigned Sub-Licenses</h2>
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($assignedSubLicenses as $subLicense)
                                <li>
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if($subLicense->is_suspended)
                                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                                    @elseif($subLicense->expires_at && $subLicense->expires_at->isPast())
                                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                                    @else
                                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-start">
                                                        <div class="flex flex-col">
                                                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400 truncate">
                                                                {{ $subLicense->parentLicense->policy_name ?? 'Sub-License' }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                Sub-license
                                                            </p>
                                                        </div>
                                                        @if($subLicense->is_suspended)
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                                Suspended
                                                            </span>
                                                        @elseif($subLicense->expires_at && $subLicense->expires_at->isPast())
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                                Expired
                                                            </span>
                                                        @else
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                                Active
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 font-mono">
                                                        {{ $subLicense->key }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    @if($subLicense->expires_at)
                                                        Expires {{ $subLicense->expires_at->format('M j, Y') }}
                                                    @else
                                                        No expiration
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Assigned {{ $subLicense->created_at->format('M j, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($licenses->count() === 0 && $assignedSubLicenses->count() === 0)
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
