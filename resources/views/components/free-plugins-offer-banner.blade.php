@props(['inline' => false])

<div @class(['max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' => !$inline])>
    <div class="bg-gradient-to-r from-emerald-100 to-teal-100 dark:from-emerald-900/40 dark:to-teal-900/40 border border-emerald-300 dark:border-emerald-700 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0 text-emerald-600 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="font-medium text-emerald-900 dark:text-emerald-100">
                    Claim Your Free Plugins!
                </h3>
                <p class="mt-1 text-sm text-emerald-700 dark:text-emerald-300">
                    As a thank you for your continued support, you can claim 5 premium plugins for free:
                    <a href="{{ route('plugins.show', ['vendor' => 'nativephp', 'package' => 'mobile-biometrics']) }}" class="font-medium underline hover:text-emerald-900 dark:hover:text-emerald-200">Biometrics</a>,
                    <a href="{{ route('plugins.show', ['vendor' => 'nativephp', 'package' => 'mobile-geolocation']) }}" class="font-medium underline hover:text-emerald-900 dark:hover:text-emerald-200">Geolocation</a>,
                    <a href="{{ route('plugins.show', ['vendor' => 'nativephp', 'package' => 'mobile-firebase']) }}" class="font-medium underline hover:text-emerald-900 dark:hover:text-emerald-200">Firebase</a>,
                    <a href="{{ route('plugins.show', ['vendor' => 'nativephp', 'package' => 'mobile-secure-storage']) }}" class="font-medium underline hover:text-emerald-900 dark:hover:text-emerald-200">Secure Storage</a>, and
                    <a href="{{ route('plugins.show', ['vendor' => 'nativephp', 'package' => 'mobile-scanner']) }}" class="font-medium underline hover:text-emerald-900 dark:hover:text-emerald-200">Scanner</a>.
                </p>
                <div class="mt-4">
                    <form action="{{ route('customer.claim-free-plugins') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-2 -ml-1 size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Claim All Plugins
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
