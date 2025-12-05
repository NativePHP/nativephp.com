@props(['plugin'])

<div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md dark:border-gray-700 dark:bg-slate-800/50">
    <div class="flex items-start justify-between">
        <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
            <x-icons.puzzle class="size-6" />
        </div>
        @if ($plugin->isPaid())
            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                Paid
            </span>
        @else
            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                Free
            </span>
        @endif
    </div>

    <div class="mt-4 flex-1">
        <h3 class="font-mono text-sm font-semibold text-gray-900 dark:text-white">
            {{ $plugin->name }}
        </h3>
        @if ($plugin->description)
            <p class="mt-2 line-clamp-3 text-sm text-gray-600 dark:text-gray-400">
                {{ $plugin->description }}
            </p>
        @endif
    </div>

    <div class="mt-4 flex items-center gap-3">
        @if ($plugin->isFree())
            <a
                href="{{ $plugin->getPackagistUrl() }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                View on Packagist
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @else
            <a
                href="{{ $plugin->getAnystackUrl() }}"
                target="_blank"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
            >
                View on Anystack
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @endif
    </div>
</div>
