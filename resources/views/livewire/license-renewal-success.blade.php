@if($renewalCompleted)
<div class="min-h-screen py-12">
@else
<div wire:poll.5s="checkRenewalStatus" class="min-h-screen py-12">
@endif
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4
                    {{ $renewalCompleted ? 'bg-green-100 dark:bg-green-900' : ($renewalFailed ? 'bg-red-100 dark:bg-red-900' : 'bg-blue-100 dark:bg-blue-900') }}">
                    @if($renewalCompleted)
                        <svg class="h-8 w-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @elseif($renewalFailed)
                        <svg class="h-8 w-8 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @else
                        <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    @endif
                </div>

                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-2">
                    @if($renewalCompleted)
                        License Renewal Complete!
                    @elseif($renewalFailed)
                        Renewal Processing Failed
                    @else
                        Processing Your Renewal...
                    @endif
                </h3>

                <p class="max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                    @if($renewalCompleted)
                        Your automatic renewal has been set up successfully and your license expiry date has been updated.
                    @elseif($renewalFailed)
                        There was an issue processing your renewal. Please contact support for assistance.
                    @else
                        We're updating your license details. This usually takes a few moments.
                    @endif
                </p>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5 sm:px-6">
                @if($renewalCompleted)
                    <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                    Renewal Successful!
                                </h3>
                                <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Your license has been successfully renewed</li>
                                        <li>Automatic renewal has been set up for future renewals</li>
                                        <li>Your existing license key continues to work</li>
                                        <li>You'll receive a confirmation email shortly</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($renewalFailed)
                    <div class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Processing Failed
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>We encountered an issue while processing your renewal. Your payment may have been successful, but we're having trouble updating your license details.</p>
                                    <p class="mt-2">Please contact our support team and reference session ID: <code class="bg-red-100 dark:bg-red-800 px-1 rounded">{{ $sessionId }}</code></p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="animate-pulse h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    Processing Your Renewal
                                </h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <p>We're currently updating your license details with the new expiry date. This process usually completes within a few minutes.</p>
                                    <p class="mt-1 text-xs opacity-75">This page will automatically refresh when processing is complete.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

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
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                @if($renewalCompleted)
                                    New Expiry Date
                                @else
                                    Current Expiry
                                @endif
                            </dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($renewalCompleted && $license->expires_at)
                                    <span class="text-green-600 dark:text-green-400 font-medium">
                                        {{ $license->expires_at->format('F j, Y') }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                        ({{ $license->expires_at->diffForHumans() }})
                                    </span>
                                @else
                                    {{ $originalExpiryDate ?: 'N/A' }}
                                @endif
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
                        @if(!$renewalCompleted && !$renewalFailed)
                            <br><span class="text-xs">Session ID: {{ $sessionId }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
