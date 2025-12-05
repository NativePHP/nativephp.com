<div class="mx-auto max-w-5xl">
    {{-- Header --}}
    <section class="mt-12">
        <div class="text-center">
            <h1 class="text-3xl font-bold md:text-4xl">Plugin Directory</h1>
            <p class="mx-auto mt-4 max-w-2xl text-gray-600 dark:text-zinc-400">
                Browse all available plugins for NativePHP Mobile.
            </p>
        </div>

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
                        placeholder="Search plugins by name..."
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

        {{-- Results count --}}
        @if ($search)
            <p class="mt-4 text-center text-sm text-gray-600 dark:text-zinc-400">
                {{ $plugins->total() }} {{ Str::plural('result', $plugins->total()) }} for "{{ $search }}"
            </p>
        @endif
    </section>

    {{-- Plugin Grid --}}
    <section class="mt-10 pb-16">
        @if ($plugins->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3" wire:loading.class="opacity-50">
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
                <x-icons.puzzle class="size-12 text-gray-400 dark:text-gray-500" />
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No plugins found</h3>
                @if ($search)
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        No plugins match your search. Try a different term.
                    </p>
                    <button
                        type="button"
                        wire:click="clearSearch"
                        class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Clear search
                    </button>
                @else
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        Be the first to submit a plugin to the directory!
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
                Back to Plugins
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
