{{-- On this page --}}
<h3 class="flex items-center gap-1.5 text-sm opacity-60">
    {{-- Icon --}}
    <x-icons.stacked-lines class="size-[18px]" />

    {{-- Label --}}
    <div>On this page</div>
</h3>

{{-- Table of contents --}}
@if (count($tableOfContents) > 0)
    <div
        class="mt-4 flex min-h-44 flex-col space-y-2 overflow-y-auto overflow-x-hidden border-l text-xs dark:border-l-white/15"
    >
        @foreach ($tableOfContents as $item)
            <a
                href="#{{ $item['anchor'] }}"
                @class([
                    'transition duration-300 ease-in-out will-change-transform hover:translate-x-0.5 hover:text-violet-400 hover:opacity-100 dark:text-white/80',
                    'pb-1 pl-3' => $item['level'] == 2,
                    'py-1 pl-6' => $item['level'] == 3,
                ])
            >
                {{ $item['title'] }}
            </a>
        @endforeach
    </div>
@endif

<div
    class="mt-7 max-w-52 border-t border-t-black/20 pt-5 dark:border-t-white/15"
>
    {{-- Sponsors --}}
    <h3 class="flex items-center gap-1.5 text-sm opacity-60">
        {{-- Icon --}}
        <x-icons.star-circle class="size-[18px]" />

        {{-- Label --}}
        <div>Sponsors</div>
    </h3>

    {{-- List --}}
    <div class="space-y-3 pt-2.5">
        <x-sponsors.lists.docs.featured-sponsors />
    </div>

    {{-- List --}}
    <div class="space-y-3 pt-2.5">
        <x-sponsors.lists.docs.corporate-sponsors />
    </div>
</div>
