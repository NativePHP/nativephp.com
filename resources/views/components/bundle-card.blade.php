@props(['bundle'])

<a
    href="{{ route('bundles.show', $bundle) }}"
    class="flex flex-col rounded-2xl border border-gray-200 bg-white p-6 shadow-sm transition duration-200 hover:shadow-md hover:border-amber-300 dark:border-gray-700 dark:bg-slate-800/50 dark:hover:border-amber-600"
>
    <div class="flex items-start justify-between">
        @if ($bundle->hasLogo())
            <img
                src="{{ $bundle->getLogoUrl() }}"
                alt="{{ $bundle->name }} logo"
                class="size-12 shrink-0 rounded-xl object-cover"
            />
        @else
            <div class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75 2.25 12l4.179 2.25m0-4.5 5.571 3 5.571-3m-11.142 0L2.25 7.5 12 2.25l9.75 5.25-4.179 2.25m0 0L21.75 12l-4.179 2.25m0 0 4.179 2.25L12 21.75 2.25 16.5l4.179-2.25m11.142 0-5.571 3-5.571-3" />
                </svg>
            </div>
        @endif

        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
            Bundle
        </span>
    </div>

    <div class="mt-4 flex-1">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ $bundle->name }}
        </h3>
        @if ($bundle->description)
            <p class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">
                {{ $bundle->description }}
            </p>
        @endif

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ $bundle->plugins->count() }} plugins included
        </p>
    </div>

    <div class="mt-4 flex items-end justify-between">
        <div>
            <span class="text-lg font-bold text-gray-900 dark:text-white">
                {{ $bundle->formatted_price }}
            </span>
            @if ($bundle->discount_percent > 0)
                <span class="ml-2 text-sm text-gray-500 line-through dark:text-gray-400">
                    {{ $bundle->formatted_retail_value }}
                </span>
            @endif
        </div>

        @if ($bundle->discount_percent > 0)
            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">
                Save {{ $bundle->discount_percent }}%
            </span>
        @endif
    </div>
</a>
