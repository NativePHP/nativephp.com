<x-layout title="Renew Your License">
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                {{-- Header --}}
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Renew Your NativePHP License
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Set up automatic renewal to keep your license active beyond its expiry date.
                    </p>
                </div>

                {{-- License Information --}}
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <dl>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                {{ $license->name ?: $subscriptionType->name() }}
                            </dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License Key</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-sm font-mono">
                                    {{ $license->key }}
                                </code>
                            </dd>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Expiry</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                {{ $license->expires_at->format('F j, Y \a\t g:i A T') }}
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    ({{ $license->expires_at->diffForHumans() }})
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Renewal Information --}}
                <div class="px-4 py-5 sm:px-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            🎉 Early Access Pricing Available!
                        </h4>
                        <p class="text-blue-800 dark:text-blue-200">
                            As an early adopter, you're eligible for our special Early Access Pricing - the same great
                            rates you enjoyed when you first purchased your license. This pricing is only available
                            until your license expires. After that you will have to renew at full price.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">What happens when you renew:</h4>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Your existing license will continue to work without interruption
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Automatic renewal will be set up to prevent future expiry
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                You'll receive Early Access Pricing for your renewal
                            </li>
                            <li class="flex items-start">
                                <svg class="flex-shrink-0 h-5 w-5 text-green-400 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                No new license key - your existing key continues to work
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8">
                        <form method="POST" action="{{ route('license.renewal.checkout', $license->key) }}">
                            @csrf
                            <button type="submit" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Set Up Automatic Renewal
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </form>

                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 text-center">
                            You'll be redirected to Stripe to complete your subscription setup.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
