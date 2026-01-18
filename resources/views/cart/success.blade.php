<x-layout title="Purchase Complete">
    <div class="mx-auto mt-10 max-w-2xl px-4 sm:px-6 lg:px-8">
        <div
            x-data="{
                status: 'pending',
                licenses: [],
                pollCount: 0,
                maxPolls: 30,
                pollInterval: null,
                init() {
                    this.checkStatus();
                    this.pollInterval = setInterval(() => {
                        if (this.status === 'complete' || this.pollCount >= this.maxPolls) {
                            clearInterval(this.pollInterval);
                            return;
                        }
                        this.checkStatus();
                    }, 2000);
                },
                async checkStatus() {
                    this.pollCount++;
                    try {
                        const response = await fetch('{{ route('cart.status', ['sessionId' => $sessionId]) }}');
                        const data = await response.json();
                        this.status = data.status;
                        if (data.licenses) {
                            this.licenses = data.licenses;
                        }
                    } catch (e) {
                        console.error('Failed to check status', e);
                    }
                }
            }"
            class="rounded-lg border border-gray-200 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800"
        >
            {{-- Loading State --}}
            <template x-if="status === 'pending' && pollCount < maxPolls">
                <div>
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30">
                        <svg class="size-8 animate-spin text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Processing Your Purchase...</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Please wait while we confirm your payment and set up your licenses.
                    </p>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-500">
                        This usually takes just a few seconds.
                    </p>
                </div>
            </template>

            {{-- Success State --}}
            <template x-if="status === 'complete'">
                <div>
                    {{-- Success Icon --}}
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-green-600 dark:text-green-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>

                    <h1 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Thank You for Your Purchase!</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Your payment was successful. You now have access to the following plugins:
                    </p>

                    {{-- Purchased Plugins --}}
                    <div class="mt-8 space-y-4">
                        <template x-for="license in licenses" :key="license.id">
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/50">
                                <div class="flex items-center gap-4">
                                    <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                        <x-icons.puzzle class="size-5" />
                                    </div>
                                    <span class="font-mono text-sm font-medium text-gray-900 dark:text-white" x-text="license.plugin_name"></span>
                                </div>
                                <a :href="'/plugins/' + license.plugin_slug" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View Plugin
                                </a>
                            </div>
                        </template>
                    </div>

                    {{-- Next Steps --}}
                    <div class="mt-8 rounded-lg bg-indigo-50 p-6 text-left dark:bg-indigo-900/20">
                        <h3 class="font-medium text-indigo-900 dark:text-indigo-200">What's Next?</h3>
                        <ul class="mt-3 space-y-2 text-sm text-indigo-700 dark:text-indigo-300">
                            <li class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Configure your Composer credentials in your project
                            </li>
                            <li class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Run <code class="rounded bg-indigo-100 px-1 dark:bg-indigo-900/50">composer require [package-name]</code>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="mt-0.5 size-4 shrink-0">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                                Follow the plugin's installation instructions
                            </li>
                        </ul>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">
                            Go to Dashboard
                        </a>
                        <a href="{{ route('plugins.directory') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                            Browse More Plugins
                        </a>
                    </div>
                </div>
            </template>

            {{-- Timeout/Error State --}}
            <template x-if="status === 'pending' && pollCount >= maxPolls">
                <div>
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-yellow-100 dark:bg-yellow-900/30">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-8 text-yellow-600 dark:text-yellow-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <h1 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Taking Longer Than Expected</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Your payment is being processed, but it's taking longer than usual. Don't worry - your purchase is confirmed.
                    </p>
                    <p class="mt-4 text-sm text-gray-500 dark:text-gray-500">
                        Please check your dashboard in a few minutes, or contact support if you don't see your plugins.
                    </p>
                    <div class="mt-8 flex justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-layout>
