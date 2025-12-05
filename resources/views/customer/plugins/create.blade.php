<x-layout title="Submit Your Plugin">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-6">
                    <div>
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="{{ route('customer.plugins.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        Plugins
                                    </a>
                                </li>
                                <li>
                                    <svg class="size-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </li>
                                <li>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Submit Plugin</span>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Submit Your Plugin</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Add your plugin to the NativePHP Plugin Directory
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="mx-auto max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
            <form
                method="POST"
                action="{{ route('customer.plugins.store') }}"
                x-data="{ pluginType: '{{ old('type', 'free') }}' }"
                class="space-y-8"
            >
                @csrf

                {{-- Plugin Name --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plugin Details</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Enter your plugin's Composer package name.
                    </p>

                    <div class="mt-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Composer Package Name
                        </label>
                        <div class="mt-1">
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="vendor/package-name"
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 font-mono shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm @error('name') border-red-500 dark:border-red-500 @enderror"
                            />
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                This should match your package name in <code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-700">composer.json</code>
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Plugin Type --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Plugin Type</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Is your plugin free or paid?
                    </p>

                    <div class="mt-6 space-y-4">
                        {{-- Free Option --}}
                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none" :class="pluginType === 'free' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <input
                                type="radio"
                                name="type"
                                value="free"
                                x-model="pluginType"
                                class="sr-only"
                            />
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Free Plugin
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        Open source, hosted on Packagist/GitHub
                                    </span>
                                </span>
                            </span>
                            <svg x-show="pluginType === 'free'" class="size-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </label>

                        {{-- Paid Option --}}
                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none" :class="pluginType === 'paid' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <input
                                type="radio"
                                name="type"
                                value="paid"
                                x-model="pluginType"
                                class="sr-only"
                            />
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-medium text-gray-900 dark:text-white">
                                        Paid Plugin
                                    </span>
                                    <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                        Commercial plugin, hosted on Anystack
                                    </span>
                                </span>
                            </span>
                            <svg x-show="pluginType === 'paid'" class="size-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </label>
                    </div>

                    @error('type')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Anystack Instructions (shown for paid plugins) --}}
                <div
                    x-show="pluginType === 'paid'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="rounded-lg border border-amber-200 bg-amber-50 p-6 dark:border-amber-900/50 dark:bg-amber-900/20"
                >
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                Anystack Setup Required
                            </h3>
                            <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                <p>
                                    To sell your plugin through the NativePHP Plugin Directory, you'll need to set up your product on Anystack and join our affiliate program.
                                </p>
                                <ol class="mt-4 list-inside list-decimal space-y-2">
                                    <li>
                                        <strong>Create an Anystack account</strong> at
                                        <a href="https://anystack.sh" target="_blank" class="font-medium underline hover:text-amber-900 dark:hover:text-amber-100">anystack.sh</a>
                                    </li>
                                    <li>
                                        <strong>Set up your product</strong> with your plugin's details, pricing, and license configuration
                                    </li>
                                    <li>
                                        <strong>Join our affiliate program</strong> by going to the <em>Advertising</em> section in your Anystack dashboard and applying to the <strong>"NativePHP Plugin Directory"</strong> program
                                    </li>
                                </ol>
                                <div class="mt-4 rounded-md bg-amber-100 p-3 dark:bg-amber-900/40">
                                    <p class="text-sm">
                                        <strong>Commission:</strong> NativePHP takes a 30% commission on sales through the Plugin Directory. Anystack also charges their standard transaction fees.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Anystack Product ID Input --}}
                    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <label for="anystack_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Anystack Product ID
                        </label>
                        <div class="mt-1">
                            <input
                                type="text"
                                id="anystack_id"
                                name="anystack_id"
                                value="{{ old('anystack_id') }}"
                                placeholder="a1b2c3d4-e5f6-7890-abcd-ef1234567890"
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 font-mono shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm @error('anystack_id') border-red-500 dark:border-red-500 @enderror"
                            />
                        </div>
                        @error('anystack_id')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                You can find this in the URL when viewing your product in Anystack (e.g., <code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-700">anystack.sh/products/<strong>a1b2c3d4-e5f6-...</strong></code>)
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('customer.plugins.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                    >
                        Submit Plugin
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
