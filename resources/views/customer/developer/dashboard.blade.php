<x-layout title="Developer Dashboard">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Developer Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Manage your plugins and track your earnings
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('customer.plugins.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Submit Plugin
                        </a>
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

            @if (session('message'))
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ session('message') }}</p>
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Earnings --}}
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Earnings</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        ${{ number_format($totalEarnings / 100, 2) }}
                    </dd>
                </div>

                {{-- Pending Earnings --}}
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Pending Payouts</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        ${{ number_format($pendingEarnings / 100, 2) }}
                    </dd>
                </div>

                {{-- Total Plugins --}}
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Published Plugins</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ $plugins->where('status', \App\Enums\PluginStatus::Approved)->count() }}
                    </dd>
                </div>

                {{-- Total Sales --}}
                <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow dark:bg-gray-800 sm:p-6">
                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales</dt>
                    <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        {{ $plugins->sum('licenses_count') }}
                    </dd>
                </div>
            </div>

            {{-- Account Status --}}
            <div class="mt-8 overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Stripe Account Status</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if ($developerAccount->canReceivePayouts())
                                <div class="flex size-10 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-emerald-600 dark:text-emerald-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Account Active</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Your account is fully set up to receive payouts</p>
                                </div>
                            @else
                                <div class="flex size-10 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-yellow-600 dark:text-yellow-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Action Required</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional information needed for payouts</p>
                                </div>
                            @endif
                        </div>
                        @if (! $developerAccount->canReceivePayouts())
                            <a href="{{ route('customer.developer.onboarding') }}" class="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Complete Setup
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Two Column Layout --}}
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                {{-- Plugins --}}
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Your Plugins</h3>
                            <a href="{{ route('customer.plugins.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                View all
                            </a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($plugins->take(5) as $plugin)
                            <a href="{{ route('customer.plugins.show', $plugin->routeParams()) }}" class="block px-4 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</p>
                                        <div class="mt-1 flex items-center gap-2">
                                            @if ($plugin->status === \App\Enums\PluginStatus::Approved)
                                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    Approved
                                                </span>
                                            @elseif ($plugin->status === \App\Enums\PluginStatus::Pending)
                                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                    Pending
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                    Rejected
                                                </span>
                                            @endif

                                            @if ($plugin->isPaid())
                                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                    Paid
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->licenses_count }} sales</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center sm:px-6">
                                <x-vaadin-plug class="mx-auto size-8 text-gray-400" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No plugins yet</p>
                                <a href="{{ route('customer.plugins.create') }}" class="mt-2 inline-flex text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                    Submit your first plugin
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Payouts --}}
                <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">Recent Payouts</h3>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($payouts as $payout)
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium text-gray-900 dark:text-white">
                                            {{ $payout->pluginLicense->plugin->name ?? 'Unknown Plugin' }}
                                        </p>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $payout->created_at->format('M j, Y') }}
                                        </p>
                                    </div>
                                    <div class="ml-4 text-right">
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($payout->developer_amount / 100, 2) }}
                                        </p>
                                        @if ($payout->status === \App\Enums\PayoutStatus::Transferred)
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                Paid
                                            </span>
                                        @elseif ($payout->status === \App\Enums\PayoutStatus::Pending)
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                Failed
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center sm:px-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto size-8 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No payouts yet</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">Payouts will appear here after you make your first sale</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
