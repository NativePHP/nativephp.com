@props([
    'title',
    'href' => null,
    'linkText' => 'View',
    'icon' => null,
    'color' => 'blue',
    'count' => null,
    'value' => null,
    'description' => null,
    'badge' => null,
    'badgeColor' => 'green',
])

@php
    $colorClasses = [
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'yellow' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'indigo' => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        'gray' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    ];

    $badgeClasses = [
        'green' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
        'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
    ];
@endphp

<div class="flex h-full flex-col overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
    <div class="flex-1 p-5">
        <div class="flex items-center">
            @if($icon)
                <div class="shrink-0">
                    <div class="{{ $colorClasses[$color] ?? $colorClasses['blue'] }} rounded-lg p-3">
                        <x-dynamic-component :component="'heroicon-o-' . $icon" class="size-6" />
                    </div>
                </div>
            @endif
            <div class="{{ $icon ? 'ml-5' : '' }} w-0 flex-1">
                <dl>
                    <dt class="truncate text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        @if($count !== null)
                            <span class="text-2xl font-semibold text-gray-900 dark:text-white">
                                {{ $count }}
                            </span>
                        @elseif($value !== null)
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $value }}
                            </span>
                        @endif
                        @if($badge)
                            <span class="{{ $badgeClasses[$badgeColor] ?? $badgeClasses['green'] }} ml-2 inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium">
                                {{ $badge }}
                            </span>
                        @endif
                    </dd>
                    @if($description)
                        <dd class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $description }}
                        </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
    @if($href)
        <div class="border-t border-gray-200 bg-gray-50 px-5 py-3 dark:border-gray-700 dark:bg-gray-800/50">
            <a href="{{ $href }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                {{ $linkText }}
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    @endif
</div>
