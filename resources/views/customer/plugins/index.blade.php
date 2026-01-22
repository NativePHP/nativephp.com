<x-layout title="Your Plugins">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Plugins</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Extend NativePHP Mobile with powerful native features
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{-- Action Cards --}}
            <div class="grid gap-6 md:grid-cols-3">
                {{-- Submit Plugin Card (Most Prominent) --}}
                <div class="relative overflow-hidden rounded-lg border-2 border-indigo-500 bg-gradient-to-br from-indigo-50 to-purple-50 p-6 shadow-sm dark:border-indigo-400 dark:from-indigo-950/50 dark:to-purple-950/50">
                    <div class="absolute -right-4 -top-4 size-24 rounded-full bg-indigo-500/10 dark:bg-indigo-400/10"></div>
                    <div class="relative">
                        <div class="flex size-12 items-center justify-center rounded-lg bg-indigo-500 text-white dark:bg-indigo-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Submit Your Plugin</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Built a plugin? Submit it to the NativePHP Plugin Directory and share it with the community.
                        </p>
                        <a href="{{ route('customer.plugins.create') }}" class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                            Submit a Plugin
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Browse Plugins Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <x-vaadin-plug class="size-6" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Browse Plugins</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Discover plugins built by the community to add native features to your mobile apps.
                    </p>
                    <a href="{{ route('plugins') }}" class="mt-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        View Directory
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                {{-- Learn to Build Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex size-12 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Learn to Build Plugins</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Read the documentation to learn how to create your own NativePHP Mobile plugins.
                    </p>
                    <a href="/docs/mobile/2/plugins" class="mt-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Read the Docs
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Author Display Name Section --}}
            <div class="mt-8">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Author Display Name</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                    This is how your name will appear on your plugins in the directory.
                                </p>
                                <form
                                    method="POST"
                                    action="{{ route('customer.plugins.display-name') }}"
                                    class="mt-4 flex gap-3"
                                    x-data="{ displayName: '{{ old('display_name', auth()->user()->display_name) }}' }"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex-1">
                                        <input
                                            type="text"
                                            name="display_name"
                                            x-model="displayName"
                                            placeholder="{{ auth()->user()->name }}"
                                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white @error('display_name') border-red-500 dark:border-red-500 @enderror"
                                        />
                                        @error('display_name')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                                    >
                                        Save
                                    </button>
                                </form>
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
                                    Leave blank to use your account name: <span class="font-medium">{{ auth()->user()->name }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stripe Connect Section (only show when paid plugins are enabled) --}}
            @feature(App\Features\AllowPaidPlugins::class)
            <div class="mt-8">
                @if ($developerAccount && $developerAccount->hasCompletedOnboarding())
                    {{-- Connected Account --}}
                    <div class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/20">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-900/50 dark:text-green-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">Stripe Connect Active</h3>
                                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                        Your developer account is set up and ready to receive payouts for paid plugin sales.
                                    </p>
                                    <div class="mt-2 flex items-center gap-4 text-sm">
                                        <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                            Payouts {{ $developerAccount->payouts_enabled ? 'Enabled' : 'Pending' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-green-700 dark:text-green-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $developerAccount->stripe_connect_status->label() }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('customer.developer.dashboard') }}" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:bg-green-700 dark:hover:bg-green-600">
                                View Dashboard
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @elseif ($developerAccount)
                    {{-- Onboarding In Progress --}}
                    <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-800 dark:bg-yellow-900/20">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-900/50 dark:text-yellow-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100">Complete Your Stripe Setup</h3>
                                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                        You've started the Stripe Connect setup but there are still some steps remaining. Complete the onboarding to start receiving payouts.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('customer.developer.onboarding.refresh') }}" class="inline-flex items-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:bg-yellow-700 dark:hover:bg-yellow-600">
                                Continue Setup
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- No Developer Account --}}
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sell Paid Plugins</h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        Want to sell premium plugins? Connect your Stripe account to receive payouts when customers purchase your paid plugins. You'll earn 70% of each sale.
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('customer.developer.onboarding') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                Connect Stripe
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-2 size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
            @endfeature

            {{-- Success Message --}}
            @if (session('success'))
                <div class="mt-6 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Submitted Plugins List --}}
            @if ($plugins->count() > 0)
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Submitted Plugins</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Track the status of your plugin submissions.</p>

                    <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow dark:border-gray-700 dark:bg-gray-800">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($plugins as $plugin)
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="shrink-0">
                                                @if ($plugin->isPending())
                                                    <div class="size-3 animate-pulse rounded-full bg-yellow-400"></div>
                                                @elseif ($plugin->isApproved())
                                                    <div class="size-3 rounded-full bg-green-400"></div>
                                                @else
                                                    <div class="size-3 rounded-full bg-red-400"></div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <p class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $plugin->name }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $plugin->type->label() }} plugin &bull; Submitted {{ $plugin->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex shrink-0 items-center gap-3">
                                            @if ($plugin->isPending())
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    Pending Review
                                                </span>
                                            @elseif ($plugin->isApproved())
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Rejected
                                                </span>
                                            @endif
                                            <a href="{{ route('customer.plugins.show', $plugin->routeParams()) }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Edit
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="ml-1 size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Rejection Reason --}}
                                    @if ($plugin->isRejected() && $plugin->rejection_reason)
                                        <div class="mt-3 rounded-md bg-red-50 p-3 dark:bg-red-900/20">
                                            <div class="flex">
                                                <div class="shrink-0">
                                                    <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Rejection Reason</h3>
                                                    <p class="mt-1 text-sm text-red-700 dark:text-red-300">{{ $plugin->rejection_reason }}</p>
                                                    <div class="mt-3">
                                                        <form method="POST" action="{{ route('customer.plugins.resubmit', $plugin->routeParams()) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center rounded-md bg-red-100 px-3 py-1.5 text-sm font-medium text-red-800 hover:bg-red-200 dark:bg-red-900/50 dark:text-red-200 dark:hover:bg-red-900/70">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1.5 size-4">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                                </svg>
                                                                Resubmit for Review
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
