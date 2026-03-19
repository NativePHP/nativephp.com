@props([
    'features' => [
        'apps' => 1,
        'keys' => 1,
    ],
])

{{-- Features --}}
<div
    class="space-y-3 text-sm"
    aria-label="{{ $planName }} plan features"
>
    <div class="flex items-center gap-2">
        <x-icons.desktop-computer
            class="size-5 shrink-0"
            aria-hidden="true"
        />
        <div class="text-zinc-500">
            Build
            <span class="font-medium text-black dark:text-white">
                unlimited
            </span>
            apps
        </div>
    </div>
    <div class="flex items-center gap-2">
        <x-icons.upload-box
            class="size-5 shrink-0"
            aria-hidden="true"
        />
        <div class="text-zinc-500">
            Release
            <span class="font-medium text-black dark:text-white">
                {{ $features['apps'] }}
            </span>
            production apps
        </div>
    </div>
    <div class="flex items-center gap-2">
        <x-icons.user-single
            class="size-5 shrink-0"
            aria-hidden="true"
        />
        <div class="text-zinc-500">
            <span class="font-medium text-black dark:text-white">
                {{ $features['keys'] }}
            </span>
            developer seats (keys)
        </div>
    </div>
</div>

{{-- Divider - Decorative --}}
<div
    class="my-5 h-px w-full rounded-full bg-black/15"
    aria-hidden="true"
></div>

{{-- Perks --}}
<div
    class="space-y-2.5 text-sm"
    aria-label="{{ $planName }} plan perks"
>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
            aria-hidden="true"
        >
            <x-icons.checkmark class="size-5 shrink-0" />
        </div>
        <div class="font-medium">One year of package updates</div>
    </div>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black"
            aria-hidden="true"
        >
            <x-icons.checkmark class="size-5 shrink-0" />
        </div>
        <div class="font-medium">Community support via Discord</div>
    </div>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl {{ $features['discord'] ?? false ? 'bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black' : 'bg-zinc-200 dark:bg-gray-700/50' }}"
            aria-hidden="true"
        >
            @if($features['discord'] ?? false)
                <x-icons.checkmark class="size-5 shrink-0" />
            @else
                <x-icons.xmark class="size-2.5 shrink-0 dark:opacity-70" />
            @endif
        </div>
        <div class="{{ $features['discord'] ?? false ? 'font-medium' : '' }}">Access Private Discord channels</div>
    </div>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl {{ $features['github'] ?? false ? 'bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black' : 'bg-zinc-200 dark:bg-gray-700/50' }}"
            aria-hidden="true"
        >
            @if($features['github'] ?? false)
                <x-icons.checkmark class="size-5 shrink-0" />
            @else
                <x-icons.xmark class="size-2.5 shrink-0 dark:opacity-70" />
            @endif
        </div>
        <div class="{{ $features['github'] ?? false ? 'font-medium' : '' }}">Direct repo access on GitHub</div>
    </div>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl {{ $features['priority'] ?? false ? 'bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black' : 'bg-zinc-200 dark:bg-gray-700/50' }}"
            aria-hidden="true"
        >
            @if($features['priority'] ?? false)
                <x-icons.checkmark class="size-5 shrink-0" />
            @else
                <x-icons.xmark class="size-2.5 shrink-0 dark:opacity-70" />
            @endif
        </div>
        <div class="{{ $features['priority'] ?? false ? 'font-medium' : '' }}">Help decide feature priority</div>
    </div>
    <div class="flex items-center gap-2">
        <div
            class="grid size-7 shrink-0 place-items-center rounded-xl {{ $features['support'] ?? false ? 'bg-[#D4FD7D] dark:bg-[#d68ffe] dark:text-black' : 'bg-zinc-200 dark:bg-gray-700/50' }}"
            aria-hidden="true"
        >
            @if($features['support'] ?? false)
                <x-icons.checkmark class="size-5 shrink-0" />
            @else
                <x-icons.xmark class="size-2.5 shrink-0 dark:opacity-70" />
            @endif
        </div>
        <div class="{{ $features['support'] ?? false ? 'font-medium' : '' }}">Business hours email support (GMT)</div>
    </div>
</div>
