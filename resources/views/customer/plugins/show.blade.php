<x-layout title="Edit Plugin">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center space-x-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">Plugins</span>
                    </a>
                    <h1 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">Edit Plugin</h1>
                    <p class="mt-1 font-mono text-sm text-gray-600 dark:text-gray-400">
                        {{ $plugin->name }}
                    </p>
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
                        @if ($plugin->hasLogo())
                            <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-10 rounded-lg object-cover" />
                        @else
                            <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                                <x-icons.puzzle class="size-5" />
                            </div>
                        @endif
                        <div>
                            <p class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $plugin->type->label() }} plugin
                                @if ($plugin->latest_version)
                                    <span class="text-gray-400 dark:text-gray-500">•</span>
                                    v{{ $plugin->latest_version }}
                                @endif
                            </p>
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

            {{-- Plugin Logo --}}
            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plugin Logo</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Upload a logo for your plugin. This will be displayed in the plugin directory.
                </p>

                <div class="mt-4">
                    @if ($plugin->hasLogo())
                        <div class="flex items-start gap-4">
                            <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-24 rounded-lg object-cover shadow-sm" />
                            <div class="flex flex-col gap-2">
                                <form method="POST" action="{{ route('customer.plugins.logo.update', $plugin) }}" enctype="multipart/form-data" class="flex items-center gap-2">
                                    @csrf
                                    <input
                                        type="file"
                                        name="logo"
                                        accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                        class="block text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/70"
                                    />
                                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                        Update
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('customer.plugins.logo.delete', $plugin) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mr-1 size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        Remove logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('customer.plugins.logo.update', $plugin) }}" enctype="multipart/form-data" class="flex items-center gap-4">
                            @csrf
                            <input
                                type="file"
                                name="logo"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                class="block text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/70"
                            />
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                Upload
                            </button>
                        </form>
                    @endif
                    @error('logo')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        PNG, JPG, SVG, or WebP. Max 1MB. Recommended: 256x256 pixels, square.
                    </p>
                </div>
            </div>

            {{-- Webhook Status --}}
            @if ($plugin->webhook_secret)
                @if ($plugin->webhook_installed)
                    {{-- Webhook was automatically installed --}}
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-900/50 dark:bg-green-900/20">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-green-900 dark:text-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Webhook Configured
                        </h2>
                        <p class="mt-2 text-sm text-green-800 dark:text-green-200">
                            The GitHub webhook has been automatically configured for your repository. Your plugin data will sync automatically when you push changes or create releases.
                        </p>
                    </div>
                @else
                    {{-- Webhook needs manual setup --}}
                    <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 p-6 dark:border-amber-900/50 dark:bg-amber-900/20">
                        <h2 class="flex items-center gap-2 text-lg font-semibold text-amber-900 dark:text-amber-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            Manual Webhook Setup Required
                        </h2>
                        <p class="mt-2 text-sm text-amber-800 dark:text-amber-200">
                            We couldn't automatically install the webhook on your repository. This might be because you don't have admin access to the repository or haven't connected your GitHub account. Please set it up manually to enable automatic syncing.
                        </p>

                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-amber-900 dark:text-amber-100">Webhook URL</label>
                                <div class="mt-1 flex items-center gap-2">
                                    <code class="block flex-1 overflow-x-auto rounded-md bg-white px-3 py-2 font-mono text-sm text-gray-900 dark:bg-gray-800 dark:text-white">{{ $plugin->getWebhookUrl() }}</code>
                                    <button
                                        type="button"
                                        onclick="navigator.clipboard.writeText('{{ $plugin->getWebhookUrl() }}')"
                                        class="inline-flex items-center rounded-md bg-amber-100 px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-200 dark:bg-amber-900/50 dark:text-amber-200 dark:hover:bg-amber-900/70"
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
                                    <li>Under "Which events would you like to trigger this webhook?", select <strong>Let me select individual events</strong></li>
                                    <li>Check <strong>Pushes</strong> and <strong>Releases</strong></li>
                                    <li>Click <strong>Add webhook</strong></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Pricing (Paid plugins only) --}}
            @if ($plugin->isPaid())
                <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pricing</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Set the price for your plugin. Minimum price is $10.
                    </p>

                    <form method="POST" action="{{ route('customer.plugins.price.update', $plugin) }}" class="mt-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (USD)</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                                </div>
                                <input
                                    type="number"
                                    name="price"
                                    id="price"
                                    min="10"
                                    max="99999"
                                    step="1"
                                    value="{{ old('price', $plugin->activePrice?->amount ? $plugin->activePrice->amount / 100 : '') }}"
                                    class="block w-full rounded-md border border-gray-300 py-2 pl-7 pr-12 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 sm:text-sm @error('price') border-red-500 dark:border-red-500 @enderror"
                                    placeholder="29"
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 dark:text-gray-400 sm:text-sm">USD</span>
                                </div>
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                Whole dollars only, no cents. You will receive 70% of each sale after payment processing fees.
                            </p>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Update Price
                            </button>
                        </div>
                    </form>
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
