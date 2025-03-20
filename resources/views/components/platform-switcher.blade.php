@php
    $isMobile = request()->is('docs/mobile/*');
    $mobileHref = '/docs/mobile/1';
    $desktopHref = '/docs/desktop/1';
@endphp

<div
    {{ $attributes }}
    class="mx-1 mb-3 rounded-xl bg-zinc-100/80 pb-4 pl-4 pr-5 pt-5 transition duration-300 ease-in-out dark:bg-gray-900/40"
>
    {{-- Title --}}
    <div class="text-sm">Choose your platform</div>
    {{-- Description --}}
    <div class="pt-1 text-xs text-gray-500">
        Switch between mobile and desktop documentation.
    </div>

    {{-- Separator --}}
    <div
        class="my-2.5 h-px w-full rounded-full bg-current opacity-5 dark:opacity-20"
    ></div>

    {{-- Switcher --}}
    <div class="flex items-center gap-3 text-xs">
        {{-- Desktop --}}
        <a
            href="{{ $desktopHref }}"
            @class([
                'grid w-1/2 place-items-center rounded-xl p-2.5 transition duration-300 ease-in-out',
                'bg-white dark:bg-gray-900' => ! $isMobile,
                'hover:bg-zinc-200/50 dark:text-gray-500 dark:hover:bg-gray-900/80 dark:hover:text-white' => $isMobile,
            ])
        >
            <div
                @class([
                    'grid size-9 place-items-center rounded-lg',
                    'bg-blue-50 text-blue-500 dark:bg-black/20 dark:text-blue-400' => ! $isMobile,
                ])
            >
                <x-icons.laptop-code class="size-5 shrink-0" />
            </div>
            <div class="pt-1">Desktop</div>
        </a>

        {{-- Center icon --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="size-6 dark:text-gray-500"
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
                'grid w-1/2 place-items-center rounded-xl p-2.5 transition duration-300 ease-in-out',
                'bg-white dark:bg-gray-900' => $isMobile,
                'hover:bg-zinc-200/50 dark:text-gray-500 dark:hover:bg-gray-900/80 dark:hover:text-white' => ! $isMobile,
            ])
        >
            <div
                @class([
                    'grid size-9 place-items-center rounded-lg',
                    'bg-blue-50 text-blue-500 dark:bg-black/20 dark:text-blue-400' => $isMobile,
                ])
            >
                <x-icons.device-mobile-phone class="size-5 shrink-0" />
            </div>
            <div class="pt-1">Mobile</div>
        </a>
    </div>
</div>
