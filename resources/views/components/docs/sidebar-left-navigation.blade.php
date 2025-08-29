@php
    $isMobile = request()->is('docs/mobile/*');
@endphp

<aside
    class="sticky top-20 hidden max-h-[calc(100dvh-7rem)] w-[18rem] shrink-0 overflow-x-hidden overflow-y-auto pt-4 pr-3 lg:block"
>
    <div class="relative flex flex-1 flex-col pb-5">
        {{-- <x-docs.platform-switcher /> --}}
        <div
            class="mx-0.5 mb-3 flex w-full items-center gap-3 rounded-xl bg-gradient-to-tl from-transparent to-violet-100 px-3.5 py-4 dark:from-slate-900/30 dark:to-indigo-900/35"
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

        <nav class="docs-navigation">{!! $slot !!}</nav>
    </div>
</aside>
