<x-layout title="Purchase History">
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
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Purchase History</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            All your NativePHP purchases in one place
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            @if($purchases->count() > 0)
                <div class="overflow-hidden rounded-md bg-white shadow dark:bg-gray-800">
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($purchases as $purchase)
                            <li>
                                @if($purchase['href'])
                                    <a href="{{ $purchase['href'] }}" class="block hover:bg-gray-50 dark:hover:bg-gray-700">
                                @else
                                    <div class="">
                                @endif
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="shrink-0">
                                                    @if($purchase['type'] === 'subscription')
                                                        <div class="grid size-10 place-items-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                            <x-heroicon-o-key class="size-5" />
                                                        </div>
                                                    @else
                                                        <div class="grid size-10 place-items-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                                                            <x-heroicon-o-puzzle-piece class="size-5" />
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="flex items-start">
                                                        <div class="flex flex-col">
                                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $purchase['name'] }}
                                                            </p>
                                                            @if($purchase['description'])
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $purchase['description'] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                        @if($purchase['type'] === 'subscription')
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                                License
                                                            </span>
                                                        @else
                                                            <span class="ml-2 inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                                                Plugin
                                                            </span>
                                                        @endif
                                                        @if(isset($purchase['is_grandfathered']) && $purchase['is_grandfathered'])
                                                            <span class="ml-1 inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                                Free
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <div class="flex items-center space-x-2">
                                                    @if($purchase['price'] !== null && $purchase['price'] > 0)
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            ${{ number_format($purchase['price'] / 100, 2) }}
                                                        </p>
                                                    @elseif($purchase['price'] === 0 || (isset($purchase['is_grandfathered']) && $purchase['is_grandfathered']))
                                                        <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                                            Free
                                                        </p>
                                                    @endif
                                                    @if($purchase['is_active'])
                                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                            Expired
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $purchase['purchased_at']->format('M j, Y') }}
                                                </p>
                                                @if($purchase['expires_at'])
                                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                                        @if($purchase['expires_at']->isPast())
                                                            Expired {{ $purchase['expires_at']->format('M j, Y') }}
                                                        @else
                                                            Expires {{ $purchase['expires_at']->format('M j, Y') }}
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @if($purchase['href'])
                                    </a>
                                @else
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center">
                    <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                        <x-heroicon-o-receipt-refund class="mx-auto size-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No purchases yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Your purchase history will appear here once you make your first purchase.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout>
