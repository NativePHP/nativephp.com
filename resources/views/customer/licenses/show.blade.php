<x-layout title="License Details">
    <header class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-4">
                                <li>
                                    <a href="{{ route('customer.licenses') }}" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                        Your Licenses
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                        </svg>
                                        <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">License Details</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $license->name ?: $license->policy_name }}
                        </h1>
                        @if($license->name)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $license->policy_name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('customer.billing-portal') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Billing
                        </a>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                License Information
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                Details about your NativePHP license.
                            </p>
                        </div>
                        <div>
                            @if($license->is_suspended)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    <svg class="-ml-1 mr-1.5 h-2 w-2 text-red-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Suspended
                                </span>
                            @elseif($license->expires_at && $license->expires_at->isPast())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <svg class="-ml-1 mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Expired
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <svg class="-ml-1 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Active
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <dl>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                License Key
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                <div class="flex items-center justify-between w-full">
                                    <code class="bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded text-sm font-mono">
                                        {{ $license->key }}
                                    </code>
                                    <button
                                        type="button"
                                        onclick="copyToClipboard('{{ $license->key }}')"
                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                License Name
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                <div class="flex items-center justify-between w-full">
                                    <span id="license-name-display" class="{{ $license->name ? '' : 'text-gray-500 dark:text-gray-400 italic' }}">
                                        {{ $license->name ?: 'No name set' }}
                                    </span>
                                    <button
                                        type="button"
                                        onclick="showEditLicenseNameModal()"
                                        class="ml-2 inline-flex items-center px-2.5 py-1.5 border border-gray-300 dark:border-gray-600 shadow-sm text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Edit
                                    </button>
                                </div>
                            </dd>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                License Type
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                {{ $license->policy_name }}
                            </dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                Created
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                {{ $license->created_at->format('F j, Y \a\t g:i A') }}
                                <span class="text-gray-500 dark:text-gray-400 ml-1">
                                    ({{ $license->created_at->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                        @if($license->expires_at)
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    Expires
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                    {{ $license->expires_at->format('F j, Y \a\t g:i A') }}
                                    <span class="text-gray-500 dark:text-gray-400 ml-1">
                                        @if($license->expires_at->isPast())
                                            (Expired {{ $license->expires_at->diffForHumans() }})
                                        @else
                                            ({{ $license->expires_at->diffForHumans() }})
                                        @endif
                                    </span>
                                </dd>
                            </div>
                        @else
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                    Expires
                                </dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2 flex items-center">
                                    Never
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Keys section --}}
            @if($license->supportsSubLicenses())
                <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Keys
                                    <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                        ({{ $license->subLicenses->count() }}{{ $license->subLicenseLimit ? '/' . $license->subLicenseLimit : '' }})
                                    </span>
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                    Manage license keys for team members or additional devices.
                                </p>
                            </div>
                            @if($license->canCreateSubLicense())
                                <button
                                    type="button"
                                    onclick="showCreateKeyModal()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Create Key
                                </button>
                            @endif
                        </div>
                    </div>

                    @php
                        $activeSubLicenses = $license->subLicenses->where('is_suspended', false);
                        $suspendedSubLicenses = $license->subLicenses->where('is_suspended', true);
                    @endphp

                    @if($license->subLicenses->isEmpty())
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-8 text-center">
                            <div class="text-gray-500 dark:text-gray-400">
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No keys</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first key.</p>
                            </div>
                        </div>
                    @else
                        {{-- Active Sub-Licenses --}}
                        @if($activeSubLicenses->isNotEmpty())
                            <div class="border-t border-gray-200 dark:border-gray-700">
                                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($activeSubLicenses as $subLicense)
                                    <li class="px-4 py-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <div class="flex-1">
                                                        @if($subLicense->name)
                                                            <div>
                                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $subLicense->name }}
                                                                </p>
                                                            </div>
                                                        @endif
                                                        <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                            <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono">
                                                                {{ $subLicense->key }}
                                                            </code>
                                                            <button
                                                                type="button"
                                                                onclick="copyToClipboard('{{ $subLicense->key }}')"
                                                                class="ml-2 text-xs text-blue-600 hover:text-blue-500"
                                                            >
                                                                Copy
                                                            </button>
                                                        </div>
                                                        @if($subLicense->assigned_email)
                                                            <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                <span class="text-xs">Assigned to: {{ $subLicense->assigned_email }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4 flex items-center space-x-2">
                                                        @if($subLicense->assigned_email)
                                                            <form method="POST" action="{{ route('customer.licenses.sub-licenses.send-email', [$license->key, $subLicense]) }}" class="inline">
                                                                @csrf
                                                                <button type="submit" class="text-green-600 hover:text-green-500 text-sm">
                                                                    Send License
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button
                                                            type="button"
                                                            onclick="showEditSubLicenseModal({{ $subLicense->id }}, '{{ $subLicense->name }}', '{{ $subLicense->assigned_email }}')"
                                                            class="text-blue-600 hover:text-blue-500 text-sm"
                                                        >
                                                            Edit
                                                        </button>
                                                        <form method="POST" action="{{ route('customer.licenses.sub-licenses.suspend', [$license->key, $subLicense]) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="text-yellow-600 hover:text-yellow-500 text-sm">
                                                                Suspend
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Suspended Sub-Licenses --}}
                        @if($suspendedSubLicenses->isNotEmpty())
                            <div class="mt-6 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                                <div class="px-4 py-5 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                                Suspended Keys
                                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                                    ({{ $suspendedSubLicenses->count() }})
                                                </span>
                                            </h3>
                                            <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                                                These keys are currently suspended and cannot be used.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-700">
                                    <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($suspendedSubLicenses as $subLicense)
                                        <li class="px-4 py-4 bg-red-50 dark:bg-red-900/20">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center">
                                                        <div class="flex-1">
                                                            @if($subLicense->name)
                                                                <div>
                                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                        {{ $subLicense->name }}
                                                                    </p>
                                                                </div>
                                                            @endif
                                                            <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono">
                                                                    {{ $subLicense->key }}
                                                                </code>
                                                                <button
                                                                    type="button"
                                                                    onclick="copyToClipboard('{{ $subLicense->key }}')"
                                                                    class="ml-2 text-xs text-blue-600 hover:text-blue-500"
                                                                >
                                                                    Copy
                                                                </button>
                                                            </div>
                                                            @if($subLicense->assigned_email)
                                                                <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                    <span class="text-xs">Assigned to: {{ $subLicense->assigned_email }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4 flex items-center space-x-2">
                                                            <button
                                                                type="button"
                                                                onclick="showEditSubLicenseModal({{ $subLicense->id }}, '{{ $subLicense->name }}', '{{ $subLicense->assigned_email }}')"
                                                                class="text-blue-600 hover:text-blue-500 text-sm"
                                                            >
                                                                Edit
                                                            </button>
                                                            <form method="POST" action="{{ route('customer.licenses.sub-licenses.unsuspend', [$license->key, $subLicense]) }}" class="inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="text-green-600 hover:text-green-500 text-sm">
                                                                    Unsuspend
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if(!$license->canCreateSubLicense())
                        <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-3 bg-yellow-50 dark:bg-yellow-900/20">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                        @if($license->remainingSubLicenses === 0)
                                            You have reached the maximum number of keys for this plan.
                                        @elseif($license->is_suspended)
                                            Keys cannot be created for suspended licenses.
                                        @elseif($license->expires_at && $license->expires_at->isPast())
                                            Keys cannot be created for expired licenses.
                                        @else
                                            Keys cannot be created at this time.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @php
                $isLegacyLicense = !$license->subscription_item_id && $license->expires_at;
                $daysUntilExpiry = $license->expires_at ? $license->expires_at->diffInDays(now()) : null;
                $needsRenewal = $isLegacyLicense && $daysUntilExpiry !== null && $daysUntilExpiry <= 30;
            @endphp

            @if($needsRenewal && !$license->expires_at->isPast())
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                🎉 Renewal Available with Early Access Pricing
                            </h3>
                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                <p class="mb-2">
                                    Your license expires in {{ $daysUntilExpiry }} day{{ $daysUntilExpiry === 1 ? '' : 's' }}.
                                    Set up automatic renewal now to avoid service interruption and lock in your Early Access Pricing!
                                </p>
                                <div class="mt-3">
                                    <a href="{{ route('license.renewal', $license->key) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 dark:text-blue-200 dark:bg-blue-800 dark:hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Set Up Renewal
                                        <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($license->is_suspended || ($license->expires_at && $license->expires_at->isPast()))
                <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                @if($license->is_suspended)
                                    License Suspended
                                @else
                                    License Expired
                                @endif
                            </h3>
                            <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                <p>
                                    @if($license->is_suspended)
                                        This license has been suspended. Please contact support for assistance.
                                    @elseif($isLegacyLicense)
                                        This license has expired. You can still renew it to restore access.
                                        <a href="{{ route('license.renewal', $license->key) }}" class="font-medium underline hover:no-underline">
                                            Renew now
                                        </a>
                                    @else
                                        This license has expired. Please renew your subscription to continue using NativePHP.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Create Key Modal --}}
    <div id="createKeyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create Key</h3>
                    <button type="button" onclick="hideCreateKeyModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('customer.licenses.sub-licenses.store', $license->key) }}" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="create_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name (Optional)
                        </label>
                        <input
                            type="text"
                            id="create_name"
                            name="name"
                            autocomplete="off"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., Development Team, John's Machine"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Give your key a descriptive name to help identify its purpose.</p>
                    </div>
                    <div class="mb-4">
                        <label for="create_assigned_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assign to Email (Optional)
                        </label>
                        <input
                            type="email"
                            id="create_assigned_email"
                            name="assigned_email"
                            autocomplete="email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., john@company.com"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign this license to a team member. They'll receive usage instructions via email.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideCreateKeyModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Key
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Key Modal --}}
    <div id="editSubLicenseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Key</h3>
                    <button type="button" onclick="hideEditSubLicenseModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editSubLicenseForm" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name (Optional)
                        </label>
                        <input
                            type="text"
                            id="edit_name"
                            name="name"
                            autocomplete="off"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., Development Team, John's Machine"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Give your key a descriptive name to help identify its purpose.</p>
                    </div>
                    <div class="mb-4">
                        <label for="edit_assigned_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Assign to Email (Optional)
                        </label>
                        <input
                            type="email"
                            id="edit_assigned_email"
                            name="assigned_email"
                            autocomplete="email"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., john@company.com"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign this license to a team member. They'll receive usage instructions via email.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideEditSubLicenseModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Key
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit License Name Modal --}}
    <div id="editLicenseNameModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit License Name</h3>
                    <button type="button" onclick="hideEditLicenseNameModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('customer.licenses.update', $license->key) }}" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="license_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            License Name (Optional)
                        </label>
                        <input
                            type="text"
                            id="license_name"
                            name="name"
                            value="{{ $license->name }}"
                            autocomplete="off"
                            autocapitalize="none"
                            autocorrect="off"
                            spellcheck="false"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            placeholder="e.g., Main License, Production Environment"
                        />
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Give your license a descriptive name to help organize your licenses.</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideEditLicenseNameModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 border border-gray-300 dark:border-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 border border-transparent rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Name
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show a brief success message (you could implement this with a toast notification)
                console.log('License key copied to clipboard');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }

        function showCreateKeyModal() {
            document.getElementById('createKeyModal').classList.remove('hidden');
            document.getElementById('create_name').focus();
        }

        function hideCreateKeyModal() {
            document.getElementById('createKeyModal').classList.add('hidden');
            document.getElementById('create_name').value = '';
            document.getElementById('create_assigned_email').value = '';
        }

        function showEditSubLicenseModal(subLicenseId, currentName, currentEmail) {
            const modal = document.getElementById('editSubLicenseModal');
            const form = document.getElementById('editSubLicenseForm');
            const nameInput = document.getElementById('edit_name');
            const emailInput = document.getElementById('edit_assigned_email');

            form.action = '/customer/licenses/{{ $license->key }}/sub-licenses/' + subLicenseId;
            nameInput.value = currentName || '';
            emailInput.value = currentEmail || '';
            modal.classList.remove('hidden');
            nameInput.focus();
        }

        function hideEditSubLicenseModal() {
            document.getElementById('editSubLicenseModal').classList.add('hidden');
            document.getElementById('edit_name').value = '';
            document.getElementById('edit_assigned_email').value = '';
        }

        function showEditLicenseNameModal() {
            document.getElementById('editLicenseNameModal').classList.remove('hidden');
            document.getElementById('license_name').focus();
        }

        function hideEditLicenseNameModal() {
            document.getElementById('editLicenseNameModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const createModal = document.getElementById('createKeyModal');
            const editModal = document.getElementById('editSubLicenseModal');
            const licenseNameModal = document.getElementById('editLicenseNameModal');

            if (event.target === createModal) {
                hideCreateKeyModal();
            }
            if (event.target === editModal) {
                hideEditSubLicenseModal();
            }
            if (event.target === licenseNameModal) {
                hideEditLicenseNameModal();
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                hideCreateKeyModal();
                hideEditSubLicenseModal();
                hideEditLicenseNameModal();
            }
        });
    </script>
</x-layout>
