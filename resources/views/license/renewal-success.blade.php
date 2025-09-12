<x-layout title="Renewal Successful">
    <div class="min-h-screen py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">
                        License Renewal Successful!
                    </h3>

                    <p class="max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Your automatic renewal has been set up successfully.<br>
                        Your license will now automatically renew before it expires.
                    </p>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
                    <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                    What's Next?
                                </h3>
                                <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Your existing license key continues to work without any changes</li>
                                        <li>Your license will automatically renew before the expiry date</li>
                                        <li>You'll receive a confirmation email with your subscription details</li>
                                        <li>You can manage your subscription from your account dashboard</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">License Information:</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">License Key</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs font-mono">
                                        {{ $license->key }}
                                    </code>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Expiry</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $license->expires_at->format('F j, Y') }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('customer.licenses') }}" class="flex-1 flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            View Your Licenses
                        </a>
                        <a href="{{ route('welcome') }}" class="flex-1 flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to NativePHP
                        </a>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Questions about your renewal? <a href="mailto:support@nativephp.com" class="text-blue-600 hover:text-blue-500">Contact our support team</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
