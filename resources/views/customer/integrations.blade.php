<x-layout title="Integrations">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center space-x-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                            <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium">Dashboard</span>
                        </a>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Integrations</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Connect your accounts to unlock additional features
                        </p>
                    </div>
                    <x-dashboard-menu />
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session()->has('success'))
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session()->has('warning'))
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">{{ session('warning') }}</p>
                    </div>
                </div>
            @endif

            @if(session()->has('error'))
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            {{-- Info Section --}}
            <div class="mb-6 bg-gray-50 dark:bg-gray-800 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">About Integrations</h3>
                <div class="prose dark:prose-invert prose-sm max-w-none text-gray-600 dark:text-gray-400">
                    <ul class="list-disc list-inside space-y-2">
                        <li><strong>GitHub:</strong> Max license holders can access the private <code>nativephp/mobile</code> repository. Plugin Dev Kit license holders can access <code>nativephp/claude-code</code>.</li>
                        <li><strong>Discord:</strong> Max license holders receive a special "Max" role in the NativePHP Discord server.</li>
                    </ul>
                    <p class="mt-4">
                        Need help? Join our <a href="https://discord.gg/nativephp" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">Discord community</a>.
                    </p>
                </div>
            </div>

            {{-- Claude Plugins Access (for Plugin Dev Kit license holders) --}}
            <div class="mb-6">
                <livewire:claude-plugins-access-banner :inline="true" />
            </div>

            @if(auth()->user()->hasMaxAccess())
                {{-- Integration Cards --}}
                <div class="space-y-6">
                    {{-- GitHub Integration --}}
                    <livewire:git-hub-access-banner :inline="true" />

                    {{-- Discord Integration --}}
                    <livewire:discord-access-banner :inline="true" />
                </div>
            @endif
        </div>
    </div>
</x-layout>
