@php
    $isMobile = request()->is('docs/mobile/*');
    $mobileHref = '/docs/mobile/1';
    $desktopHref = '/docs/desktop/1';
@endphp

<div
    class="dark:bg-mirage grid h-11 grid-cols-2 items-center gap-0.5 rounded-lg bg-gray-100 p-1 text-xs"
>
    <a
        href="{{ $desktopHref }}"
        @class([
            'flex h-9 items-center gap-1.5 rounded-[calc(var(--radius-lg)-1px)] pr-2 pl-1 transition duration-300 ease-in-out',
            'bg-white dark:bg-slate-700/30' => ! $isMobile,
        ])
    >
        <div
            @class([
                'grid h-7 w-7.5 place-items-center rounded-[calc(var(--radius-md)-1px)]',
                'dark:bg-haiti bg-blue-50 text-blue-500 dark:text-blue-400' => ! $isMobile,
            ])
        >
            <x-icons.pc class="size-5 shrink-0" />
        </div>
        <div>Desktop</div>
    </a>
    <a
        href="{{ $mobileHref }}"
        @class([
            'flex h-9 items-center gap-1.5 rounded-[calc(var(--radius-lg)-1px)] pr-2 pl-1 transition duration-300 ease-in-out',
            'bg-white dark:bg-slate-700/30' => $isMobile,
        ])
    >
        <div
            @class([
                'grid h-7 w-7.5 place-items-center rounded-[calc(var(--radius-md)-1px)]',
                'dark:bg-haiti bg-blue-50 text-blue-500 dark:text-blue-400' => $isMobile,
            ])
        >
            <x-icons.device-mobile-phone class="size-4 shrink-0" />
        </div>
        <div>Mobile</div>
    </a>
</div>
