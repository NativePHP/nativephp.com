<div class="mx-auto max-w-7xl">
    {{-- Header --}}
    <section class="mt-12">
        <div class="text-center">
            <h1 class="text-3xl font-bold md:text-4xl">Plugin Marketplace</h1>
            <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-zinc-400">
                Browse all available plugins and bundles for NativePHP Mobile.
            </p>
        </div>

        {{-- View Toggle --}}
        @if ($bundles->isNotEmpty())
            <div class="mt-8 flex justify-center">
                <div class="inline-flex rounded-lg bg-gray-100 p-1 dark:bg-slate-800">
                    <button
                        type="button"
                        wire:click="showPlugins"
                        class="{{ $view === 'plugins' ? 'bg-white text-gray-900 shadow dark:bg-slate-700 dark:text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors"
                    >
                        <x-vaadin-plug class="size-4" />
                        Plugins
                    </button>
                    <button
                        type="button"
                        wire:click="showBundles"
                        class="{{ $view === 'bundles' ? 'bg-white text-gray-900 shadow dark:bg-slate-700 dark:text-white' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }} flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                        </svg>
                        Bundles
                    </button>
                </div>
            </div>
        @endif

        {{-- Search --}}
        <div class="mt-8">
            <div class="mx-auto max-w-xl">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search {{ $view === 'bundles' ? 'bundles' : 'plugins' }} by name..."
                        class="block w-full rounded-xl border border-gray-300 bg-white py-3 pl-11 pr-10 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-slate-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"
                    />
                    @if ($search)
                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Active Filters --}}
        @if ($search || $authorUser)
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                @if ($authorUser)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-100 py-1 pl-3 pr-1.5 text-sm font-medium text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        {{ $authorUser->display_name }}
                        <button
                            type="button"
                            wire:click="clearAuthor"
                            class="ml-0.5 rounded-full p-0.5 hover:bg-indigo-200 dark:hover:bg-indigo-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif
                @if ($search)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 py-1 pl-3 pr-1.5 text-sm font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        "{{ $search }}"
                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="ml-0.5 rounded-full p-0.5 hover:bg-gray-200 dark:hover:bg-gray-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $plugins->total() }} {{ Str::plural('result', $plugins->total()) }}
                </span>
            </div>
        @endif
    </section>

    {{-- Content Grid --}}
    <section class="mt-10 pb-16">
        @if ($view === 'bundles')
            {{-- Bundles Grid --}}
            @if ($bundles->count() > 0)
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-50">
                    @foreach ($bundles as $bundle)
                        <x-bundle-card :bundle="$bundle" wire:key="bundle-{{ $bundle->id }}" />
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-slate-800/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12 text-gray-400 dark:text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No bundles found</h3>
                    @if ($search)
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            No bundles match your search. Try a different term.
                        </p>
                        <button
                            type="button"
                            wire:click="clearSearch"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                        >
                            Clear search
                        </button>
                    @else
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Check back soon for plugin bundles!
                        </p>
                    @endif
                </div>
            @endif
        @else
            {{-- Plugins Grid --}}
            @if ($plugins->count() > 0)
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4" wire:loading.class="opacity-50">
                    @foreach ($plugins as $plugin)
                        <x-plugin-card :plugin="$plugin" wire:key="plugin-{{ $plugin->id }}" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($plugins->hasPages())
                    <div class="mt-10">
                        {{ $plugins->links() }}
                    </div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 p-12 text-center dark:border-gray-700 dark:bg-slate-800/50">
                    <x-vaadin-plug class="size-12 text-gray-400 dark:text-gray-500" />
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No plugins found</h3>
                    @if ($search || $authorUser)
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            @if ($authorUser && $search)
                                No plugins by {{ $authorUser->display_name }} match your search.
                            @elseif ($authorUser)
                                {{ $authorUser->display_name }} hasn't published any plugins yet.
                            @else
                                No plugins match your search. Try a different term.
                            @endif
                        </p>
                        <div class="mt-4 flex items-center gap-2">
                            @if ($search)
                                <button
                                    type="button"
                                    wire:click="clearSearch"
                                    class="inline-flex items-center gap-2 rounded-lg bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                                >
                                    Clear search
                                </button>
                            @endif
                            @if ($authorUser)
                                <button
                                    type="button"
                                    wire:click="clearAuthor"
                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                >
                                    View all plugins
                                </button>
                            @endif
                        </div>
                    @else
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Be the first to submit a plugin to the marketplace!
                        </p>
                        <a
                            href="{{ route('customer.plugins.create') }}"
                            class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Submit a Plugin
                        </a>
                    @endif
                </div>
            @endif
        @endif
    </section>

    {{-- Back to plugins landing --}}
    <section class="border-t border-gray-200 pb-16 pt-10 dark:border-gray-700">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <a
                href="{{ route('plugins') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Plugins Home
            </a>
            <a
                href="{{ route('customer.plugins.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Submit Your Plugin
            </a>
        </div>
    </section>
</div>
