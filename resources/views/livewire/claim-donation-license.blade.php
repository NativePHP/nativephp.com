<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        @if ($claimed)
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-800">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    License Claimed!
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Your license is being generated and will be sent to your email shortly.
                </p>
                <div class="mt-6">
                    <a href="{{ route('customer.licenses') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900">
                        View Your Licenses
                    </a>
                </div>
            </div>
        @else
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Claim Your License
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400">
                    Thank you for supporting NativePHP via OpenCollective!
                    <br>Enter your details below to claim your Mini license.
                </p>
            </div>

            <form wire:submit="claim" class="mt-8 space-y-6">
                <div class="space-y-4">
                    @auth
                        <div class="rounded-md bg-blue-50 dark:bg-blue-900/20 p-4">
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                Claiming as <strong>{{ auth()->user()->email }}</strong>
                            </p>
                        </div>
                    @endauth

                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            OpenCollective Transaction ID
                        </label>
                        <input
                            wire:model="order_id"
                            id="order_id"
                            type="text"
                            required
                            class="mt-1 appearance-none relative block w-full px-3 py-2 border @error('order_id') border-red-300 @else border-gray-300 @enderror dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                            placeholder="e.g. 51763"
                        >
                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    @guest
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your Name
                            </label>
                            <input
                                wire:model="name"
                                id="name"
                                type="text"
                                required
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border @error('name') border-red-300 @else border-gray-300 @enderror dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Enter your full name"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email Address
                            </label>
                            <input
                                wire:model="email"
                                id="email"
                                type="email"
                                autocomplete="email"
                                required
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border @error('email') border-red-300 @else border-gray-300 @enderror dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Enter your email address"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Create a Password
                            </label>
                            <input
                                wire:model="password"
                                id="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border @error('password') border-red-300 @else border-gray-300 @enderror dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Create a password"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Confirm Password
                            </label>
                            <input
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white dark:bg-gray-800 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
                                placeholder="Confirm your password"
                            >
                        </div>
                    @endguest
                </div>

                <div>
                    <button
                        type="submit"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>Claim License</span>
                        <span wire:loading>Processing...</span>
                    </button>
                </div>

                @guest
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                        Already have an account?
                        <a href="{{ route('customer.login') }}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                            Sign in
                        </a>
                    </p>
                @endguest
            </form>

            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white text-center">
                    How to find your Transaction ID
                </h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">
                    You can find your Transaction ID on your OpenCollective transactions page.
                </p>
                <div class="mt-6">
                    <img
                        src="/img/opencollective.png"
                        alt="Screenshot showing where to find your OpenCollective Transaction ID"
                        class="rounded-lg shadow-lg border border-gray-200 dark:border-gray-700"
                    >
                </div>
            </div>
        @endif
    </div>
</div>
