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
            {{-- Session Error Message --}}
            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation Errors Summary --}}
            @if ($errors->any())
                <div class="mb-6 rounded-md bg-red-50 p-4 dark:bg-red-900/20">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- GitHub Connection Required (for all plugins) --}}
            @if (!auth()->user()->github_id)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-6 dark:border-amber-900/50 dark:bg-amber-900/20">
                    <div class="flex">
                        <div class="shrink-0">
                            <svg class="size-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800 dark:text-amber-200">
                                GitHub Connection Required
                            </h3>
                            <div class="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                <p>
                                    To submit a plugin, you need to connect your GitHub account so we can access your repository and automatically set up webhooks.
                                </p>
                                <a href="{{ route('github.redirect', ['return' => route('customer.plugins.create')]) }}" class="mt-4 inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                                    <svg class="mr-2 size-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd" />
                                    </svg>
                                    Connect GitHub Account
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
            <form
                method="POST"
                action="{{ route('customer.plugins.store') }}"
                x-data="{
                    pluginType: '{{ old('type', 'free') }}',
                    repositories: [],
                    selectedRepo: '{{ old('repository', '') }}',
                    loadingRepos: false,
                    reposLoaded: false,
                    init() {
                        this.loadRepositories();
                    },
                    async loadRepositories() {
                        if (this.loadingRepos || this.reposLoaded) return;
                        this.loadingRepos = true;
                        try {
                            const response = await fetch('{{ route('github.repositories') }}');
                            const data = await response.json();
                            this.repositories = data.repositories || [];
                            this.reposLoaded = true;
                        } catch (error) {
                            console.error('Failed to load repositories:', error);
                        }
                        this.loadingRepos = false;
                    }
                }"
                class="space-y-8"
            >
                @csrf

                {{-- Plugin Type (only show selector when paid plugins are enabled) --}}
                @feature(App\Features\AllowPaidPlugins::class)
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
                                        Commercial plugin, hosted on plugins.nativephp.com
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
                @else
                {{-- When paid plugins are disabled, always submit as free --}}
                <input type="hidden" name="type" value="free" />
                @endfeature

                {{-- Repository Selection (for all plugins) --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="size-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                                Connected as <strong>{{ auth()->user()->github_username }}</strong>
                            </span>
                        </div>
                    </div>

                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Select Repository</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Choose the repository containing your plugin. We'll automatically set up a webhook to keep your plugin in sync.
                    </p>

                    <div class="mt-6">
                        <label for="repository_select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Repository
                        </label>
                        <div class="mt-1">
                            <template x-if="loadingRepos">
                                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="size-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Loading repositories...</span>
                                </div>
                            </template>
                            <template x-if="!loadingRepos && reposLoaded">
                                <select
                                    id="repository_select"
                                    x-model="selectedRepo"
                                    name="repository"
                                    class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm @error('repository') border-red-500 dark:border-red-500 @enderror"
                                >
                                    <option value="">Select a repository...</option>
                                    <template x-for="repo in repositories" :key="repo.id">
                                        <option :value="repo.full_name" x-text="repo.full_name + (repo.private ? ' (private)' : '')"></option>
                                    </template>
                                </select>
                            </template>
                        </div>
                        @error('repository')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Paid Plugin Info --}}
                @feature(App\Features\AllowPaidPlugins::class)
                <div
                    x-show="pluginType === 'paid'"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">How paid plugins work</h3>
                    <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>We pull your code from GitHub when you tag a release</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>We host and distribute your plugin via <code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-600">plugins.nativephp.com</code></span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>Customers install via Composer with their license key</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="mt-0.5 size-5 shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <span>You get paid automatically via Stripe Connect</span>
                        </li>
                    </ul>
                </div>
                @endfeature

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
            @endif
        </div>
    </div>
</x-layout>
