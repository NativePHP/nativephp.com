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
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Banners --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

        {{-- Plugin Composer Configuration --}}
        @feature(App\Features\ShowPlugins::class)
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plugins</h2>
                    <a href="{{ route('plugins.marketplace') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Browse plugins
                    </a>
                </div>

                {{-- Plugin License Key - Always shown --}}
                <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-800 dark:bg-indigo-900/20" x-data="{ showRotateModal: false }">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-indigo-900 dark:text-indigo-200">Your Plugin Credentials</h3>
                            <p class="mt-1 text-xs text-indigo-700 dark:text-indigo-300">
                                Use these credentials with Composer to install plugins from the NativePHP Plugin Marketplace.
                            </p>
                        </div>
                        <button
                            type="button"
                            @click="showRotateModal = true"
                            class="rounded bg-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            title="Rotate key"
                        >
                            <x-heroicon-o-arrow-path class="size-4" />
                        </button>
                    </div>
                    <details class="mt-3">
                        <summary class="cursor-pointer text-xs font-medium text-indigo-700 dark:text-indigo-300 hover:text-indigo-900 dark:hover:text-indigo-100">
                            How to configure Composer
                        </summary>
                        <div class="mt-2 space-y-2">
                            <p class="text-xs text-indigo-700 dark:text-indigo-300">1. Add the NativePHP plugins repository:</p>
                            <div class="group flex items-center rounded bg-gray-900">
                                <div class="min-w-0 flex-1 overflow-x-auto p-3">
                                    <code class="block font-mono text-xs text-gray-100 whitespace-pre pr-4">composer config repositories.nativephp-plugins composer https://plugins.nativephp.com</code>
                                </div>
                                <button
                                    type="button"
                                    onclick="navigator.clipboard.writeText('composer config repositories.nativephp-plugins composer https://plugins.nativephp.com'); this.querySelector('svg').classList.add('hidden'); this.querySelector('span').classList.remove('hidden'); setTimeout(() => { this.querySelector('svg').classList.remove('hidden'); this.querySelector('span').classList.add('hidden'); }, 2000);"
                                    class="shrink-0 self-stretch bg-gray-900 px-3 text-gray-400 hover:bg-gray-800 hover:text-gray-200"
                                    title="Copy command"
                                >
                                    <x-heroicon-o-clipboard class="size-4" />
                                    <span class="hidden text-xs">Copied!</span>
                                </button>
                            </div>
                            <p class="text-xs text-indigo-700 dark:text-indigo-300">2. Configure your credentials:</p>
                            <div class="group flex items-center rounded bg-gray-900">
                                <div class="min-w-0 flex-1 overflow-x-auto p-3">
                                    <code class="block font-mono text-xs text-gray-100 whitespace-pre pr-4">composer config http-basic.plugins.nativephp.com {{ auth()->user()->email }} {{ $pluginLicenseKey }}</code>
                                </div>
                                <button
                                    type="button"
                                    onclick="navigator.clipboard.writeText('composer config http-basic.plugins.nativephp.com {{ auth()->user()->email }} {{ $pluginLicenseKey }}'); this.querySelector('svg').classList.add('hidden'); this.querySelector('span').classList.remove('hidden'); setTimeout(() => { this.querySelector('svg').classList.remove('hidden'); this.querySelector('span').classList.add('hidden'); }, 2000);"
                                    class="shrink-0 self-stretch bg-gray-900 px-3 text-gray-400 hover:bg-gray-800 hover:text-gray-200"
                                    title="Copy command"
                                >
                                    <x-heroicon-o-clipboard class="size-4" />
                                    <span class="hidden text-xs">Copied!</span>
                                </button>
                            </div>
                        </div>
                    </details>

                    {{-- Rotate Key Confirmation Modal --}}
                    <div
                        x-show="showRotateModal"
                        x-cloak
                        class="fixed inset-0 z-50 overflow-y-auto"
                        aria-labelledby="modal-title"
                        role="dialog"
                        aria-modal="true"
                    >
                        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
                            {{-- Background overlay --}}
                            <div
                                x-show="showRotateModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 bg-gray-500/60 backdrop-blur-sm transition-opacity dark:bg-gray-900/60"
                                @click="showRotateModal = false"
                            ></div>

                            {{-- Center modal --}}
                            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

                            {{-- Modal panel --}}
                            <div
                                x-show="showRotateModal"
                                x-transition:enter="ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave="ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                class="relative inline-block transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left align-bottom shadow-xl transition-all dark:bg-gray-800 sm:my-8 sm:w-full sm:max-w-lg sm:p-6 sm:align-middle"
                            >
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900 sm:mx-0 sm:h-10 sm:w-10">
                                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                            Rotate Plugin License Key
                                        </h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                Are you sure you want to rotate your plugin license key? This action cannot be undone.
                                            </p>
                                            <div class="mt-3 rounded-md bg-yellow-50 p-3 dark:bg-yellow-900/30">
                                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                    After rotating your key, you will need to:
                                                </p>
                                                <ul class="mt-2 list-disc pl-5 text-sm text-yellow-700 dark:text-yellow-300">
                                                    <li>Update your <code class="font-mono">auth.json</code> file in all projects</li>
                                                    <li>Reconfigure Composer credentials on any CI/CD systems</li>
                                                    <li>Update any deployment scripts that reference the old key</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                    <form method="POST" action="{{ route('customer.plugin-license-key.rotate') }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto"
                                        >
                                            Rotate Key
                                        </button>
                                    </form>
                                    <button
                                        type="button"
                                        @click="showRotateModal = false"
                                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Purchased Plugins --}}
                @if($pluginLicenses->count() > 0)
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">Your Purchased Plugins</h3>
                    <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($pluginLicenses as $pluginLicense)
                                <li>
                                    <a href="{{ route('plugins.show', $pluginLicense->plugin->routeParams()) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="px-4 py-4 sm:px-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        @if($pluginLicense->plugin->hasLogo())
                                                            <img src="{{ $pluginLicense->plugin->getLogoUrl() }}" alt="{{ $pluginLicense->plugin->name }}" class="size-10 rounded-lg object-cover">
                                                        @elseif($pluginLicense->plugin->hasGradientIcon())
                                                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br {{ $pluginLicense->plugin->getGradientClasses() }} text-white">
                                                                <x-dynamic-component :component="'heroicon-o-' . $pluginLicense->plugin->icon_name" class="size-5" />
                                                            </div>
                                                        @else
                                                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                                                <x-vaadin-plug class="size-5" />
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
                @endif
            </div>
        @endfeature

        {{-- Content --}}
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Licenses</h2>
            @if($licenses->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($licenses as $license)
                            @php
                                $isLegacyLicense = $license->isLegacy();
                                $daysUntilExpiry = $license->expires_at ? $license->expires_at->diffInDays(now()) : null;
                                $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null && !$license->expires_at->isPast();
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
