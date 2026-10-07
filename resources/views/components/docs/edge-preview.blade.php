@props([
    'ios' => null,
    'android' => null,
    'source' => null,
    'alt' => 'Screenshot',
    'edge' => null,
    'sidebarWidthIos' => null,
    'sidebarWidthAndroid' => null,
])

@php
    $default = $ios ? 'ios' : 'android';
    $showTop = in_array($edge, ['top', 'both'], true);
    $showBottom = in_array($edge, ['bottom', 'both'], true);
    $storageKey = 'nativephp-docs-edge-preview-collapsed:'.($ios ?? $android ?? $alt);
    [$iosRadius, $androidRadius] = match (true) {
        $showTop && ! $showBottom => ['rounded-t-[2.25rem]', 'rounded-t-[1.75rem]'],
        $showBottom && ! $showTop => ['rounded-b-[2.25rem]', 'rounded-b-[1.75rem]'],
        default => ['rounded-[2.25rem]', 'rounded-[1.75rem]'],
    };

    // When a screenshot shows an open drawer/sheet that only covers part of
    // the width, the real device's dimming scrim sits over everything below
    // it — including the status bar and home-indicator zones. Splitting the
    // fake chrome bars' background to match keeps that scrim visually
    // continuous through the chrome instead of stopping dead at its edge.
    $scrim = '#8c8d8f';
    $splitBackground = fn (?int $width, string $base): ?string => $width !== null
        ? "background: linear-gradient(to right, {$base} 0%, {$base} {$width}%, {$scrim} {$width}%, {$scrim} 100%);"
        : null;
    $iosTopStyle = $splitBackground($sidebarWidthIos, '#ffffff');
    $iosBottomStyle = $splitBackground($sidebarWidthIos, '#f9fafb');
    $androidStyle = $splitBackground($sidebarWidthAndroid, '#f8fafc');
@endphp

<div x-data="{ platform: '{{ $default }}', collapsed: false, clockIos: '', clockAndroid: '', init() { try { this.collapsed = localStorage.getItem('{{ $storageKey }}' + window.location.pathname) === '1'; } catch (e) {} this.tickClock(); setInterval(() => this.tickClock(), 1000); }, tickClock() { const d = new Date(); let h = d.getHours() % 12; if (h === 0) h = 12; const m = String(d.getMinutes()).padStart(2, '0'); this.clockIos = h + ':' + m; this.clockAndroid = String(d.getHours()).padStart(2, '0') + ':' + m; }, toggleCollapsed() { this.collapsed = !this.collapsed; try { localStorage.setItem('{{ $storageKey }}' + window.location.pathname, this.collapsed ? '1' : '0'); } catch (e) {} } }" class="not-prose my-8 overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-white/10 dark:bg-white/[0.03]">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-2.5 dark:border-white/10">
        @if ($ios && $android)
            <div class="flex gap-1 transition-opacity" :class="collapsed ? 'pointer-events-none opacity-40' : ''">
                <button type="button" @click="platform = 'ios'" :class="platform === 'ios' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'" class="rounded-md px-3 py-1 text-xs font-medium transition-colors">iOS</button>
                <button type="button" @click="platform = 'android'" :class="platform === 'android' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200'" class="rounded-md px-3 py-1 text-xs font-medium transition-colors">Android</button>
            </div>
        @else
            <span class="text-xs font-medium text-gray-500 transition-opacity dark:text-gray-400" :class="collapsed ? 'opacity-40' : ''">{{ $ios ? 'iOS' : 'Android' }}</span>
        @endif
        <div class="flex items-center gap-2">
            @if ($source)
                <a href="https://github.com/NativePHP/super-native/blob/main/{{ $source }}" target="_blank" rel="noopener" title="View this example in super-native" class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-2 py-1 text-xs font-medium text-gray-500 transition-colors hover:border-gray-300 hover:text-gray-800 dark:border-white/10 dark:text-gray-400 dark:hover:border-white/20 dark:hover:text-white">
                    <x-icons.github class="size-3.5" />
                    Source
                </a>
            @endif
            <button type="button" @click="toggleCollapsed()" class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-2 py-1 text-xs font-medium text-gray-500 transition-colors hover:border-gray-300 hover:text-gray-800 dark:border-white/10 dark:text-gray-400 dark:hover:border-white/20 dark:hover:text-white">
                <span x-text="collapsed ? 'Show preview' : 'Hide preview'"></span>
                <svg class="size-3 transition-transform" :class="collapsed ? '-rotate-90' : ''" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 4.5L6 8l3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" /></svg>
            </button>
        </div>
    </div>
    <div x-show="!collapsed" x-transition class="flex justify-center bg-[radial-gradient(circle_at_center,var(--color-gray-100)_0%,transparent_70%)] p-6 sm:p-10 dark:bg-[radial-gradient(circle_at_center,--alpha(var(--color-white)/4%)_0%,transparent_70%)]">
        <div class="w-fit overflow-hidden shadow-xl ring-1 ring-black/10 transition-[border-radius] duration-200" :class="platform === 'ios' ? '{{ $iosRadius }}' : '{{ $androidRadius }}'">
            @if ($ios)
                <div x-show="platform === 'ios'">
                    @if ($showTop)
                        <div class="relative flex h-11 items-center justify-center bg-white px-6 text-gray-900" @if ($iosTopStyle) style="{{ $iosTopStyle }}" @endif>
                            <span class="absolute left-6 text-[10px] font-semibold tabular-nums" x-text="clockIos"></span>
                            <span class="h-7 w-[31%] rounded-full bg-black"></span>
                            <div class="absolute right-6 flex items-center gap-1.5">
                                <svg class="h-2.5 w-3.5" viewBox="0 0 14 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="6" width="2" height="4" rx="0.5" /><rect x="4" y="4" width="2" height="6" rx="0.5" /><rect x="8" y="2" width="2" height="8" rx="0.5" /><rect x="12" y="0" width="2" height="10" rx="0.5" /></svg>
                                <svg class="size-3" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 4.5C4.5 1 11.5 1 15 4.5M3.5 7C5.8 4.8 10.2 4.8 12.5 7" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /><circle cx="8" cy="9.3" r="1" fill="currentColor" /></svg>
                                <svg class="h-3 w-5" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="1" width="19" height="10" rx="2.5" stroke="currentColor" stroke-width="1.3" /><rect x="2.5" y="2.5" width="16" height="7" rx="1.5" fill="currentColor" /><rect x="21.5" y="4" width="1.5" height="4" rx="0.75" fill="currentColor" /></svg>
                            </div>
                        </div>
                    @endif
                    <img src="/img/docs/{{ $ios }}" alt="{{ $alt }} (iOS)" class="no-format block max-h-[28rem]" />
                    @if ($showBottom)
                        <div class="flex justify-center bg-gray-50 py-2" @if ($iosBottomStyle) style="{{ $iosBottomStyle }}" @endif>
                            <span class="h-1 w-[32%] rounded-full bg-gray-900/40"></span>
                        </div>
                    @endif
                </div>
            @endif
            @if ($android)
                <div x-show="platform === 'android'">
                    @if ($showTop)
                        <div class="relative flex h-9 items-center justify-end bg-slate-50 px-6 text-gray-900" @if ($androidStyle) style="{{ $androidStyle }}" @endif>
                            <span class="absolute left-6 text-[10px] font-medium tabular-nums" x-text="clockAndroid"></span>
                            <span class="absolute left-1/2 top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-black"></span>
                            <div class="flex items-center gap-2">
                                <svg class="h-2.5 w-3.5" viewBox="0 0 14 10" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><rect x="0" y="6" width="2" height="4" rx="0.5" /><rect x="4" y="4" width="2" height="6" rx="0.5" /><rect x="8" y="2" width="2" height="8" rx="0.5" /><rect x="12" y="0" width="2" height="10" rx="0.5" /></svg>
                                <svg class="h-2.5 w-3.5" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 4.5C4.5 1 11.5 1 15 4.5M3.5 7C5.8 4.8 10.2 4.8 12.5 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" /><circle cx="8" cy="9.3" r="1" fill="currentColor" /></svg>
                                <svg class="h-3 w-5" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="1" y="1" width="19" height="10" rx="2.5" stroke="currentColor" stroke-width="1.3" /><rect x="2.5" y="2.5" width="16" height="7" rx="1.5" fill="currentColor" /><rect x="21.5" y="4" width="1.5" height="4" rx="0.75" fill="currentColor" /></svg>
                            </div>
                        </div>
                    @endif
                    <img src="/img/docs/{{ $android }}" alt="{{ $alt }} (Android)" class="no-format block max-h-[28rem]" />
                    @if ($showBottom)
                        <div class="flex items-center justify-evenly bg-slate-50 px-6 py-3 text-gray-400" @if ($androidStyle) style="{{ $androidStyle }}" @endif>
                            <svg class="h-2 w-[3%]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 2L2 12l18 10" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <span class="block h-2 w-[3%] rounded-full ring-2 ring-current"></span>
                            <span class="block h-2 w-[3%] rounded-[3px] ring-2 ring-current"></span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
