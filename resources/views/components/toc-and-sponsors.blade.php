<h3 class="inline-flex items-center gap-1.5 text-sm opacity-50">
    <svg
        xmlns="http://www.w3.org/2000/svg"
        class="size-[18px]"
        viewBox="0 0 24 24"
    >
        <g
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
        >
            <path
                stroke-linecap="round"
                d="M3 2v20"
                opacity="0.5"
            />
            <path
                d="M7 7.5c0-.935 0-1.402.201-1.75a1.5 1.5 0 0 1 .549-.549C8.098 5 8.565 5 9.5 5h9c.935 0 1.402 0 1.75.201a1.5 1.5 0 0 1 .549.549C21 6.098 21 6.565 21 7.5s0 1.402-.201 1.75a1.5 1.5 0 0 1-.549.549c-.348.201-.815.201-1.75.201h-9c-.935 0-1.402 0-1.75-.201a1.5 1.5 0 0 1-.549-.549C7 8.902 7 8.435 7 7.5Zm0 9c0-.935 0-1.402.201-1.75a1.5 1.5 0 0 1 .549-.549C8.098 14 8.565 14 9.5 14h6c.935 0 1.402 0 1.75.201a1.5 1.5 0 0 1 .549.549c.201.348.201.815.201 1.75s0 1.402-.201 1.75a1.5 1.5 0 0 1-.549.549c-.348.201-.815.201-1.75.201h-6c-.935 0-1.402 0-1.75-.201a1.5 1.5 0 0 1-.549-.549C7 17.902 7 17.435 7 16.5Z"
            />
        </g>
    </svg>
    On this page
</h3>
@if (count($tableOfContents) > 0)
    <div class="mt-2 flex flex-col space-y-2 border-l text-xs">
        @foreach ($tableOfContents as $item)
            <a
                href="#{{ $item['anchor'] }}"
                @class([
                    'transition duration-300 ease-in-out will-change-transform hover:translate-x-0.5 hover:text-[#9d91f1] hover:opacity-100 dark:text-white',
                    'pb-1 pl-3' => $item['level'] == 2,
                    'py-1 pl-4' => $item['level'] == 3,
                ])
            >
                {{ $item['title'] }}
            </a>
        @endforeach
    </div>
@endif

<x-sidebar-title class="mt-8">Featured sponsors</x-sidebar-title>
<div class="mt-4 flex w-3/4 flex-col gap-4 pl-3">
    <x-sponsors.lists.docs.featured-sponsors />
</div>

<x-sidebar-title class="mt-8">Corporate sponsors</x-sidebar-title>
<div class="mt-4 flex w-3/4 flex-col gap-6 pl-3">
    <x-sponsors.lists.docs.corporate-sponsors />
</div>
