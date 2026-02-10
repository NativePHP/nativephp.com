<x-layout title="Purchase Complete">
    <div class="mx-auto mt-10 max-w-2xl px-4 sm:px-6 lg:px-8">
        <div
            x-data="{
                status: 'pending',
                licenses: [],
                products: [],
                needsGitHubConnection: false,
                hasGitHubConnected: false,
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
                        console.log(data);
                        this.status = data.status;
                        if (data.licenses) {
                            this.licenses = data.licenses;
                        }
                        if (data.products) {
                            this.products = data.products;
                        }
                        if (data.needs_github_connection !== undefined) {
                            this.needsGitHubConnection = data.needs_github_connection;
                        }
                        if (data.has_github_connected !== undefined) {
                            this.hasGitHubConnected = data.has_github_connected;
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
                        Your payment was successful.
                    </p>

                    {{-- Purchased Products --}}
                    <template x-if="products.length > 0">
                        <div class="mt-8">
                            <h3 class="text-left text-sm font-medium text-gray-500 dark:text-gray-400">Products</h3>
                            <div class="mt-3 space-y-4">
                                <template x-for="product in products" :key="product.id">
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/50">
                                        <div class="flex items-center gap-4">
                                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-purple-500 to-indigo-600 text-white">
                                                <x-heroicon-s-cube class="size-5" />
                                            </div>
                                            <div class="text-left">
                                                <span class="font-mono text-sm font-medium text-gray-900 dark:text-white" x-text="product.product_name"></span>
                                                <template x-if="product.github_repo">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                                        Includes access to <code class="rounded bg-gray-200 px-1 dark:bg-gray-600" x-text="'nativephp/' + product.github_repo"></code>
                                                    </p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Purchased Plugins --}}
                    <template x-if="licenses.length > 0">
                        <div class="mt-8">
                            <h3 class="text-left text-sm font-medium text-gray-500 dark:text-gray-400">Plugins</h3>
                            <div class="mt-3 space-y-4">
                                <template x-for="license in licenses" :key="license.id">
                                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/50">
                                        <div class="flex items-center gap-4">
                                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                                <x-vaadin-plug class="size-5" />
                                            </div>
                                            <span class="font-mono text-sm font-medium text-gray-900 dark:text-white" x-text="license.plugin_name"></span>
                                        </div>
                                        <a :href="'/plugins/' + license.plugin_slug" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            View Plugin
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- GitHub Connection CTA --}}
                    <template x-if="needsGitHubConnection">
                        <div class="mt-8 rounded-lg border-2 border-purple-200 bg-purple-50 p-6 text-left dark:border-purple-700 dark:bg-purple-900/20">
                            <div class="flex items-start gap-4">
                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-purple-100 dark:bg-purple-900/50">
                                    <svg class="size-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-purple-900 dark:text-purple-200">Connect GitHub to Access Your Repositories</h3>
                                    <p class="mt-1 text-sm text-purple-700 dark:text-purple-300">
                                        Your purchase includes access to private GitHub repositories. Connect your GitHub account to get access.
                                    </p>
                                    <a href="{{ route('customer.integrations') }}" class="mt-4 inline-flex items-center gap-2 rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-purple-500">
                                        Go to Integrations
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- GitHub Access Granted --}}
                    <template x-if="hasGitHubConnected && products.some(p => p.github_repo)">
                        <div class="mt-8 rounded-lg bg-green-50 p-6 text-left dark:bg-green-900/20">
                            <div class="flex items-start gap-4">
                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-green-100 dark:bg-green-900/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5 text-green-600 dark:text-green-400">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-green-900 dark:text-green-200">Repository Access Granted</h3>
                                    <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                                        Check your GitHub notifications for repository invitations. Accept the invitations to access your new repositories.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Next Steps for Plugins --}}
                    <template x-if="licenses.length > 0">
                        <div class="mt-8 rounded-lg bg-indigo-50 p-6 text-left dark:bg-indigo-900/20">
                            <h3 class="font-medium text-indigo-900 dark:text-indigo-200">Plugin Installation</h3>
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
                    </template>

                    {{-- Actions --}}
                    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:justify-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700">
                            Go to Dashboard
                        </a>
                        <a href="{{ route('plugins.marketplace') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
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
                        Please check your dashboard in a few minutes, or contact support if you don't see your items.
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
