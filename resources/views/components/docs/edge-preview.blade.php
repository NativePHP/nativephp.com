@props([
    'ios' => null,
    'android' => null,
    'source' => null,
    'alt' => 'Screenshot',
    'chrome' => true,
])

@php
    $default = $ios ? 'ios' : 'android';
@endphp

<div x-data="{ platform: '{{ $default }}' }" class="not-prose my-8 overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/[0.03]">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-2.5 dark:border-white/10">
        @if ($ios && $android)
            <div class="flex gap-1">
                <button type="button" @click="platform = 'ios'" :class="platform === 'ios' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'" class="rounded-md px-3 py-1 text-xs font-medium transition-colors">iOS</button>
                <button type="button" @click="platform = 'android'" :class="platform === 'android' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'" class="rounded-md px-3 py-1 text-xs font-medium transition-colors">Android</button>
            </div>
        @else
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $ios ? 'iOS' : 'Android' }}</span>
        @endif
        @if ($source)
            <a href="https://github.com/NativePHP/super-native/blob/main/{{ $source }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 transition-colors hover:text-gray-800 dark:text-gray-400 dark:hover:text-white">
                <x-icons.github class="size-3.5" />
                View this example in super-native
            </a>
        @endif
    </div>
    <div class="flex justify-center bg-[radial-gradient(circle_at_center,var(--color-gray-100)_0%,transparent_70%)] p-6 sm:p-10 dark:bg-[radial-gradient(circle_at_center,--alpha(var(--color-white)/4%)_0%,transparent_70%)]">
        <div class="w-fit overflow-hidden rounded-2xl shadow-xl ring-1 ring-black/5 dark:ring-white/10">
            @if ($chrome)
                <div class="flex items-center justify-between gap-4 bg-gray-900 px-4 py-1.5 dark:bg-black">
                    <div class="flex gap-1">
                        <span class="block size-1 rounded-full bg-white/40"></span>
                        <span class="block size-1 rounded-full bg-white/40"></span>
                        <span class="block size-1 rounded-full bg-white/40"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="size-3 text-white/40" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 4.5C4.5 1 11.5 1 15 4.5M3.5 7C5.8 4.8 10.2 4.8 12.5 7M6.2 9.5C7.2 8.5 8.8 8.5 9.8 9.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" /></svg>
                        <svg class="h-3 w-5 text-white/40" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="1" width="19" height="10" rx="2.5" stroke="currentColor" stroke-width="1.3" /><rect x="21.5" y="4" width="1.5" height="4" rx="0.75" fill="currentColor" /></svg>
                    </div>
                </div>
            @endif
            @if ($ios)
                <img x-show="platform === 'ios'" src="/img/docs/{{ $ios }}" alt="{{ $alt }} (iOS)" class="block max-h-[28rem]" />
            @endif
            @if ($android)
                <img x-show="platform === 'android'" src="/img/docs/{{ $android }}" alt="{{ $alt }} (Android)" class="block max-h-[28rem]" />
            @endif
        </div>
    </div>
</div>
