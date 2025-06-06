@php
    $isMobile = request()->is('docs/mobile/*');
    $mobileHref = '/docs/mobile/1';
    $desktopHref = '/docs/desktop/1';
@endphp

<div
    class="grid h-11 grid-cols-2 items-center gap-0.5 rounded-lg bg-gray-100 p-1 text-xs ring-1 ring-black/5 dark:bg-black/20 dark:ring-white/10"
>
    <a
        href="{{ $desktopHref }}"
        @class([
            'flex h-9 items-center gap-2 rounded-md px-2 transition duration-300 ease-in-out',
            'bg-white dark:bg-slate-700/30' => ! $isMobile,
        ])
    >
        <x-icons.pc class="size-5 shrink-0" />
        <div>Desktop</div>
    </a>
    <a
        href="{{ $mobileHref }}"
        @class([
            'flex h-9 items-center gap-2 rounded-md px-2 transition duration-300 ease-in-out',
            'bg-white dark:bg-slate-700/30' => $isMobile,
        ])
    >
        <x-icons.device-mobile-phone class="size-4 shrink-0" />
        <div>Mobile</div>
    </a>
</div>
