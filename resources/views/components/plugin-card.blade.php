@props(['plugin'])

<a
    href="{{ route('plugins.show', $plugin) }}"
    class="flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md hover:border-indigo-300 dark:border-gray-700 dark:bg-slate-800/50 dark:hover:border-indigo-600"
>
    <div class="flex items-start justify-between">
        @if ($plugin->hasLogo())
            <img
                src="{{ $plugin->getLogoUrl() }}"
                alt="{{ $plugin->name }} logo"
                class="size-12 shrink-0 rounded-xl object-cover"
            />
        @elseif ($plugin->hasGradientIcon())
            <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white">
                <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-6" />
            </div>
        @else
            <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                <x-vaadin-plug class="size-6" />
            </div>
        @endif
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

    <div class="mt-4 flex items-center gap-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400">
        View details
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
        </svg>
    </div>
</a>
