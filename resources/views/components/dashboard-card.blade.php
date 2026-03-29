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
    'secondBadge' => null,
    'secondBadgeColor' => 'yellow',
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
@endphp

<flux:card class="flex h-full flex-col !p-0">
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
                <flux:text class="truncate text-sm font-medium">{{ $title }}</flux:text>
                <div class="flex flex-wrap items-baseline gap-y-1">
                    @if($count !== null)
                        <flux:heading size="xl">{{ $count }}</flux:heading>
                    @elseif($value !== null)
                        <flux:heading size="lg">{{ $value }}</flux:heading>
                    @endif
                    @if($badge || $secondBadge)
                        <span class="flex w-full items-center gap-1.5 lg:ml-2 lg:w-auto">
                            @if($badge)
                                <flux:badge size="sm" :color="$badgeColor">{{ $badge }}</flux:badge>
                            @endif
                            @if($secondBadge)
                                <flux:badge size="sm" :color="$secondBadgeColor">{{ $secondBadge }}</flux:badge>
                            @endif
                        </span>
                    @endif
                </div>
                @if($description)
                    <flux:text class="mt-1 text-sm">{{ $description }}</flux:text>
                @endif
            </div>
        </div>
    </div>
    @if($href)
        <div class="border-t border-zinc-200 px-5 py-3 dark:border-zinc-700">
            <a href="{{ $href }}" class="text-sm font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                {{ $linkText }}
                <span aria-hidden="true"> &rarr;</span>
            </a>
        </div>
    @endif
</flux:card>
