@php
    $isMobile = request()->is('docs/mobile/*');
    $mobileHref = '/docs/mobile/1';
    $desktopHref = '/docs/desktop/1';
@endphp

<div
    {{ $attributes }}
    class="mx-1 mb-3 rounded-xl bg-zinc-100/80 transition-all duration-300 ease-in-out dark:bg-mirage"
    :class="{
        'pb-4 pl-4 pr-5 pt-5' : showPlatformSwitcherHeader,
        'pb-2 pl-2 pr-3 pt-3' : !showPlatformSwitcherHeader
    }"
>
    <div
        x-show="showPlatformSwitcherHeader"
        x-collapse
        class="space-y-1.5"
    >
        <div class="space-y-1 pl-1">
            {{-- Title --}}
            <div class="text-sm">Choose your platform</div>
            {{-- Description --}}
            <div class="text-xs opacity-60">
                Switch between mobile and desktop documentation.
            </div>
        </div>
        {{-- Separator --}}
        <div
            class="h-px w-full rounded-full bg-current opacity-5 dark:opacity-15"
        ></div>
    </div>

    {{-- Switcher --}}
    <div
        class="flex items-center gap-3 text-xs"
        :class="{ 'mt-2.5': showPlatformSwitcherHeader }"
    >
        {{-- Desktop --}}
        <a
            href="{{ $desktopHref }}"
            @class([
                'flex w-1/2 items-center justify-center gap-x-1.5 gap-y-1 rounded-xl transition duration-300 ease-in-out',
                'bg-white dark:bg-slate-700/30' => ! $isMobile,
                'hover:bg-zinc-200/50 dark:text-gray-400/80 dark:hover:bg-gray-900/80 dark:hover:text-white' => $isMobile,
            ])
            :class="{ 'flex-col p-2.5': showPlatformSwitcherHeader, 'flex-row p-2': !showPlatformSwitcherHeader }"
        >
            <div
                @class([
                    'grid h-9 w-10 place-items-center rounded-lg',
                    'bg-blue-50 text-blue-500 dark:bg-haiti dark:text-blue-400' => ! $isMobile,
                ])
            >
                <x-icons.pc class="size-6 shrink-0" />
            </div>
            <div>Desktop</div>
        </a>

        {{-- Center icon --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-6 dark:text-gray-400/80"
            viewBox="0 0 256 256"
        >
            <path
                fill="currentColor"
                d="M144 128a16 16 0 1 1-16-16a16 16 0 0 1 16 16m-84-16a16 16 0 1 0 16 16a16 16 0 0 0-16-16m136 0a16 16 0 1 0 16 16a16 16 0 0 0-16-16"
            />
        </svg>

        {{-- Mobile --}}
        <a
            href="{{ $mobileHref }}"
            @class([
                'flex w-1/2 items-center justify-center gap-x-1.5 gap-y-1 rounded-xl transition duration-300 ease-in-out',
                'bg-white dark:bg-slate-700/30' => $isMobile,
                'hover:bg-zinc-200/50 dark:text-gray-400/80 dark:hover:bg-gray-900/80 dark:hover:text-white' => ! $isMobile,
            ])
            :class="{ 'flex-col p-2.5': showPlatformSwitcherHeader, 'flex-row p-2': !showPlatformSwitcherHeader }"
        >
            <div
                @class([
                    'grid h-9 w-10 place-items-center rounded-lg',
                    'bg-blue-50 text-blue-500 dark:bg-haiti dark:text-blue-400' => $isMobile,
                ])
            >
                <x-icons.device-mobile-phone class="size-5 shrink-0" />
            </div>
            <div>Mobile</div>
        </a>
    </div>
</div>
