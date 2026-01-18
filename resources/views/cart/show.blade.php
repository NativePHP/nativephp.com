<x-layout title="Your Cart">
    <div class="mx-auto mt-10 max-w-4xl px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <header class="mb-8">
            <a href="{{ route('plugins.directory') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Plugin Directory
            </a>
            <h1 class="mt-4 text-3xl font-bold text-gray-900 dark:text-white">Your Cart</h1>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                <p class="text-sm text-green-700 dark:text-green-300">{!! session('success') !!}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
                <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                <p class="text-sm text-blue-700 dark:text-blue-300">{{ session('info') }}</p>
            </div>
        @endif

        {{-- Price Change Notifications --}}
        @if (count($priceChanges) > 0)
            <div class="mb-6 rounded-lg bg-yellow-50 p-4 dark:bg-yellow-900/20">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Price Updates</h3>
                <ul class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    @foreach ($priceChanges as $change)
                        @if ($change['type'] === 'price_changed')
                            <li>{{ $change['name'] }}: Price changed from ${{ number_format($change['old_price'] / 100) }} to ${{ number_format($change['new_price'] / 100) }}</li>
                        @else
                            <li>{{ $change['name'] }}: No longer available and was removed from your cart</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Bundle Upgrade Suggestions --}}
        @if ($bundleUpgrades->isNotEmpty())
            @foreach ($bundleUpgrades as $bundle)
                <div class="mb-6 rounded-lg border-2 border-amber-400 bg-gradient-to-r from-amber-50 to-orange-50 p-4 dark:border-amber-500 dark:from-amber-950/30 dark:to-orange-950/30">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            @if ($bundle->hasLogo())
                                <img src="{{ $bundle->getLogoUrl() }}" alt="{{ $bundle->name }}" class="size-12 shrink-0 rounded-lg object-cover" />
                            @else
                                <div class="grid size-12 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">
                                    Save with the {{ $bundle->name }} bundle!
                                </h3>
                                <p class="mt-0.5 text-sm text-amber-700 dark:text-amber-300">
                                    Your cart includes all {{ $bundle->plugins->count() }} plugins in this bundle.
                                    Switch to the bundle and save <strong>{{ $bundle->formatted_savings }}</strong> ({{ $bundle->discount_percent }}% off).
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('cart.bundle.exchange', $bundle) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 sm:w-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                                </svg>
                                Switch to Bundle
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        @endif

        @if ($cart->isEmpty())
            {{-- Empty Cart --}}
            <div class="rounded-lg border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto size-12 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Your cart is empty</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Browse our plugin directory to find plugins for your app.</p>
                <a href="{{ route('plugins.directory') }}" class="mt-6 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Browse Plugins
                </a>
            </div>
        @else
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                {{-- Cart Items --}}
                <div class="lg:col-span-8">
                    <div class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($cart->items as $item)
                                @if ($item->isBundle())
                                    {{-- Bundle Item --}}
                                    <li
                                        @if(session('just_added_bundle_id') === $item->plugin_bundle_id)
                                            x-data="{ highlight: true }"
                                            x-init="setTimeout(() => highlight = false, 3000)"
                                            :class="highlight ? 'bg-amber-50 dark:bg-amber-900/20 ring-2 ring-amber-500 ring-inset' : ''"
                                            class="flex gap-4 p-6 transition-all duration-1000"
                                        @else
                                            class="flex gap-4 p-6"
                                        @endif
                                    >
                                        {{-- Bundle Logo --}}
                                        <div class="shrink-0">
                                            @if ($item->pluginBundle->hasLogo())
                                                <img src="{{ $item->pluginBundle->getLogoUrl() }}" alt="{{ $item->pluginBundle->name }}" class="size-16 rounded-lg object-cover" />
                                            @else
                                                <div class="grid size-16 place-items-center rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Bundle Details --}}
                                        <div class="flex flex-1 flex-col">
                                            <div class="flex justify-between">
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                            <a href="{{ route('bundles.show', $item->pluginBundle) }}" class="hover:text-amber-600 dark:hover:text-amber-400">
                                                                {{ $item->pluginBundle->name }}
                                                            </a>
                                                        </h3>
                                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                                            Bundle
                                                        </span>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $item->pluginBundle->plugins->count() }} plugins included
                                                    </p>
                                                    @if ($item->pluginBundle->discount_percent > 0)
                                                        <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                                            Save {{ $item->pluginBundle->discount_percent }}% ({{ $item->pluginBundle->formatted_savings }})
                                                        </p>
                                                    @endif
                                                </div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->getFormattedPrice() }}
                                                </p>
                                            </div>

                                            <div class="mt-4 flex justify-end">
                                                <form action="{{ route('cart.bundle.remove', $item->pluginBundle) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @else
                                    {{-- Plugin Item --}}
                                    <li
                                        @if(session('just_added_plugin_id') === $item->plugin_id)
                                            x-data="{ highlight: true }"
                                            x-init="setTimeout(() => highlight = false, 3000)"
                                            :class="highlight ? 'bg-green-50 dark:bg-green-900/20 ring-2 ring-green-500 ring-inset' : ''"
                                            class="flex gap-4 p-6 transition-all duration-1000"
                                        @else
                                            class="flex gap-4 p-6"
                                        @endif
                                    >
                                        {{-- Plugin Logo --}}
                                        <div class="shrink-0">
                                            @if ($item->plugin->hasLogo())
                                                <img src="{{ $item->plugin->getLogoUrl() }}" alt="{{ $item->plugin->name }}" class="size-16 rounded-lg object-cover" />
                                            @else
                                                <div class="grid size-16 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                                    <x-icons.puzzle class="size-8" />
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Plugin Details --}}
                                        <div class="flex flex-1 flex-col">
                                            <div class="flex justify-between">
                                                <div>
                                                    <h3 class="font-mono text-sm font-medium text-gray-900 dark:text-white">
                                                        <a href="{{ route('plugins.show', $item->plugin) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                            {{ $item->plugin->name }}
                                                        </a>
                                                    </h3>
                                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                        by {{ $item->plugin->user->display_name }}
                                                    </p>
                                                </div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->getFormattedPrice() }}
                                                </p>
                                            </div>

                                            <div class="mt-4 flex justify-end">
                                                <form action="{{ route('cart.remove', $item->plugin) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    {{-- Clear Cart --}}
                    <div class="mt-4 flex justify-end">
                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                Clear cart
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="mt-8 lg:col-span-4 lg:mt-0">
                    <div class="sticky top-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white">Order Summary</h2>

                        <dl class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600 dark:text-gray-400">Subtotal ({{ $cart->itemCount() }} {{ Str::plural('item', $cart->itemCount()) }})</dt>
                                <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $cart->getFormattedSubtotal() }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <dt class="text-base font-medium text-gray-900 dark:text-white">Total</dt>
                                <dd class="text-base font-medium text-gray-900 dark:text-white">{{ $cart->getFormattedSubtotal() }}</dd>
                            </div>
                        </div>

                        <form action="{{ route('cart.checkout') }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-indigo-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                @auth
                                    Checkout
                                @else
                                    Log in to Checkout
                                @endauth
                            </button>
                        </form>

                        @guest
                            <p class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                You'll need to log in or create an account to complete your purchase.
                            </p>
                        @endguest

                        <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">
                            Secure checkout powered by Stripe
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layout>
