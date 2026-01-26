<x-layout title="Developer Onboarding">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Become a Plugin Developer</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Set up your account to sell plugins on NativePHP
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            <x-vaadin-plug class="mr-2 -ml-1 size-4" />
                            My Plugins
                        </a>
                        <x-dashboard-menu />
                    </div>
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

            @if (session('error'))
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                    <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            @if (session('message'))
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('message') }}</p>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="p-8">
                    {{-- Hero Section --}}
                    <div class="text-center">
                        <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-indigo-600 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>
                        <h2 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">
                            @if ($hasExistingAccount)
                                Complete Your Onboarding
                            @else
                                Start Selling Plugins
                            @endif
                        </h2>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            @if ($hasExistingAccount)
                                You've started the onboarding process. Complete the remaining steps to start receiving payouts.
                            @else
                                Connect your Stripe account to receive payments when users purchase your plugins.
                            @endif
                        </p>
                    </div>

                    {{-- Benefits --}}
                    <div class="mt-8 rounded-lg bg-gray-50 p-6 dark:bg-gray-700/50">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Why sell on NativePHP?</h3>
                        <ul class="mt-4 space-y-3">
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">70% Revenue Share</strong> - You keep the majority of every sale</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Built-in Distribution</strong> - Automatic Composer repository hosting</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Targeted Audience</strong> - Reach NativePHP developers directly</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                <span class="text-gray-600 dark:text-gray-400"><strong class="text-gray-900 dark:text-white">Automatic Payouts</strong> - Get paid directly to your bank account</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Status for existing account --}}
                    @if ($hasExistingAccount && $developerAccount)
                        <div class="mt-8 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-yellow-600 dark:text-yellow-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <div>
                                    <p class="font-medium text-yellow-800 dark:text-yellow-200">Onboarding Incomplete</p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">Your Stripe account requires additional information before you can receive payouts.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- CTA Button --}}
                    <div class="mt-8">
                        <form action="{{ route('customer.developer.onboarding.start') }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-center text-base font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                @if ($hasExistingAccount)
                                    Continue Onboarding
                                @else
                                    Connect with Stripe
                                @endif
                            </button>
                        </form>
                    </div>

                    {{-- Stripe Info --}}
                    <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        You'll be redirected to Stripe to complete the onboarding process securely.
                    </p>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="mt-8 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Frequently Asked Questions</h3>

                <div class="mt-4 space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">How does the revenue share work?</h4>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            You receive 70% of each sale. NativePHP retains 30% to cover payment processing, hosting, and platform maintenance.
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">When do I get paid?</h4>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Payouts are processed automatically through Stripe Connect. Funds are typically available within 2-7 business days after a sale.
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">What do I need to get started?</h4>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            You'll need a Stripe account (or create one during onboarding), a GitHub repository for your plugin, and a nativephp.json configuration file.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
