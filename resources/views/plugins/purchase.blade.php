<x-layout title="Purchase {{ $plugin->name }}">
    <section class="mx-auto mt-10 w-full max-w-2xl">
        <header class="relative">
            {{-- Blurred circle - Decorative --}}
            <div
                class="absolute top-0 right-1/2 -z-30 h-60 w-60 translate-x-1/2 rounded-full blur-[150px] md:w-80 dark:bg-slate-500/50"
                aria-hidden="true"
            ></div>

            {{-- Back button --}}
            <div>
                <a
                    href="{{ route('plugins.show', $plugin->routeParams()) }}"
                    class="inline-flex items-center gap-2 opacity-60 transition duration-200 hover:-translate-x-0.5 hover:opacity-100"
                >
                    <x-icons.right-arrow class="size-3 shrink-0 -scale-x-100" aria-hidden="true" />
                    <div class="text-sm">Back to Plugin</div>
                </a>
            </div>

            {{-- Plugin icon and title --}}
            <div class="mt-8 flex items-center gap-4">
                <div class="grid size-16 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                    <x-vaadin-plug class="size-8" />
                </div>
                <div>
                    <h1 class="font-mono text-2xl font-bold sm:text-3xl">
                        Purchase {{ $plugin->name }}
                    </h1>
                    @if ($plugin->description)
                        <p class="mt-1 text-gray-600 dark:text-gray-400">
                            {{ $plugin->description }}
                        </p>
                    @endif
                </div>
            </div>
        </header>

        <x-divider />

        {{-- Session Messages --}}
        @if (session('error'))
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Purchase Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-700 dark:bg-slate-800/50">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Summary</h2>

            <div class="mt-6 space-y-4">
                {{-- Plugin Info --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Lifetime access</p>
                    </div>
                    <div class="text-right">
                        @if ($hasDiscount && $regularPrice)
                            <p class="text-sm text-gray-500 line-through dark:text-gray-400">
                                ${{ number_format($regularPrice->amount / 100, 2) }}
                            </p>
                        @endif
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            ${{ number_format($price->amount / 100, 2) }}
                        </p>
                    </div>
                </div>

                {{-- Discount Badge --}}
                @if ($hasDiscount)
                    <div class="rounded-lg bg-emerald-50 p-3 dark:bg-emerald-900/20">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-emerald-600 dark:text-emerald-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <p class="text-sm font-medium text-emerald-800 dark:text-emerald-200">
                                {{ $price->tier->label() }} pricing applied
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Divider --}}
                <div class="border-t border-gray-200 pt-4 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <p class="text-base font-semibold text-gray-900 dark:text-white">Total</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white">
                            ${{ number_format($price->amount / 100, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Purchase Button --}}
            <div class="mt-8">
                <form action="{{ route('plugins.purchase.checkout', $plugin->routeParams()) }}" method="POST">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-center text-base font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Proceed to Checkout
                    </button>
                </form>
            </div>

            {{-- Payment Info --}}
            <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span>Secure payment via Stripe</span>
            </div>
        </div>

        {{-- What You Get --}}
        <div class="mt-8 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-slate-800/50">
            <h3 class="font-semibold text-gray-900 dark:text-white">What's Included</h3>
            <ul class="mt-4 space-y-3">
                <li class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Lifetime access to plugin updates</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Install via Composer from plugins.nativephp.com</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 size-5 shrink-0 text-emerald-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Access to source code</span>
                </li>
            </ul>
        </div>
    </section>
</x-layout>
