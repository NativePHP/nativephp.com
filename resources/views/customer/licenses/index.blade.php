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
                        <a href="{{ route('customer.billing-portal') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Billing
                        </a>
                        <a href="{{ route('pricing') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Buy more
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @if($licenses->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($licenses as $license)
                            <li>
                                <a href="{{ route('customer.licenses.show', $license->key) }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    @if($license->is_suspended)
                                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                                    @elseif($license->expires_at && $license->expires_at->isPast())
                                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                                    @else
                                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-center">
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
