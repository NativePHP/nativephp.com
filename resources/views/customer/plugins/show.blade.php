<x-layout title="Edit Plugin">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Plugin</h1>
                        <p class="mt-1 font-mono text-sm text-gray-600 dark:text-gray-400">
                            {{ $plugin->name }}
                        </p>
                    </div>
                    <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                        Back to Plugins
                    </a>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
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

            {{-- Plugin Status --}}
            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                            <x-icons.puzzle class="size-5" />
                        </div>
                        <div>
                            <p class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $plugin->type->label() }} plugin</p>
                        </div>
                    </div>
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
                </div>
            </div>

            {{-- Webhook Setup --}}
            @if ($plugin->webhook_secret)
                <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-6 dark:border-blue-900/50 dark:bg-blue-900/20">
                    <h2 class="flex items-center gap-2 text-lg font-semibold text-blue-900 dark:text-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                        </svg>
                        GitHub Webhook Setup
                    </h2>
                    <p class="mt-2 text-sm text-blue-800 dark:text-blue-200">
                        Add a webhook to your GitHub repository to automatically sync your plugin data when you push changes.
                    </p>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-900 dark:text-blue-100">Webhook URL</label>
                            <div class="mt-1 flex items-center gap-2">
                                <code class="block flex-1 overflow-x-auto rounded-md bg-white px-3 py-2 font-mono text-sm text-gray-900 dark:bg-gray-800 dark:text-white">{{ $plugin->getWebhookUrl() }}</code>
                                <button
                                    type="button"
                                    onclick="navigator.clipboard.writeText('{{ $plugin->getWebhookUrl() }}')"
                                    class="inline-flex items-center rounded-md bg-blue-100 px-3 py-2 text-sm font-medium text-blue-700 hover:bg-blue-200 dark:bg-blue-900/50 dark:text-blue-200 dark:hover:bg-blue-900/70"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="rounded-md bg-white p-4 dark:bg-gray-800">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Setup Instructions</h3>
                            <ol class="mt-2 list-inside list-decimal space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                <li>Go to your repository's <strong>Settings → Webhooks</strong></li>
                                <li>Click <strong>Add webhook</strong></li>
                                <li>Paste the Webhook URL above into the <strong>Payload URL</strong> field</li>
                                <li>Set <strong>Content type</strong> to <code class="rounded bg-gray-100 px-1 dark:bg-gray-700">application/json</code></li>
                                <li>Under "Which events would you like to trigger this webhook?", select <strong>Just the push event</strong></li>
                                <li>Click <strong>Add webhook</strong></li>
                            </ol>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Description Form --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plugin Description</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Describe what your plugin does. This will be displayed in the plugin directory.
                </p>

                <form method="POST" action="{{ route('customer.plugins.update', $plugin) }}" class="mt-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="description" class="sr-only">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            maxlength="1000"
                            placeholder="Describe what your plugin does, its key features, and how developers can use it..."
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:text-sm @error('description') border-red-500 dark:border-red-500 @enderror"
                        >{{ old('description', $plugin->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Maximum 1000 characters
                        </p>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Save Description
                        </button>
                    </div>
                </form>
            </div>

            {{-- Rejection Reason --}}
            @if ($plugin->isRejected() && $plugin->rejection_reason)
                <div class="mt-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
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
                                <form method="POST" action="{{ route('customer.plugins.resubmit', $plugin) }}" class="inline">
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
        </div>
    </div>
</x-layout>
