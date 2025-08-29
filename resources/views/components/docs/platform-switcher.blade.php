@php
    $isMobile = request()->is('docs/mobile/*');
    $mobileHref = '/docs/mobile/1';
    $desktopHref = '/docs/desktop/1';
@endphp

<a
    href="{{ $isMobile ? $desktopHref : $mobileHref }}"
    class="group relative mx-0.5 mb-3 w-full rounded-xl bg-gradient-to-tl from-transparent to-violet-100 px-3.5 py-4 dark:from-slate-900/30 dark:to-indigo-900/35"
>
    <div
        class="flex items-center gap-3 transition group-hover:translate-y-3 group-hover:opacity-0"
    >
        @if ($isMobile)
            <x-icons.device-mobile-phone class="size-6 shrink-0" />
        @else
            <x-icons.pc class="size-6 shrink-0" />
        @endif
        <div class="text-left">
            <div class="text-xs opacity-50">You're reading the</div>
            <div class="text-sm leading-6 capitalize">
                {{ $isMobile ? 'Mobile' : 'Desktop' }} Documentation
            </div>
        </div>
    </div>

    <div class="absolute top-1/2 right-1/2 translate-x-1/2 -translate-y-1/2">
        <div
            class="flex -translate-y-3 items-center justify-center gap-2 opacity-0 transition group-hover:translate-y-0 group-hover:opacity-100"
        >
            {{ $isMobile ? 'Mobile' : 'Desktop' }}
            <x-icons.right-arrow class="size-3" />
            {{ $isMobile ? 'Desktop' : 'Mobile' }}
        </div>
    </div>
</a>
