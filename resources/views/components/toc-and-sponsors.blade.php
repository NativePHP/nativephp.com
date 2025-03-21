{{-- On this page --}}
<h3 class="inline-flex items-center gap-1.5 text-sm opacity-50">
    {{-- Icon --}}
    <x-icons.stacked-lines class="size-[18px]" />

    {{-- Label --}}
    <div>On this page</div>
</h3>

{{-- Table of contents --}}
@if (count($tableOfContents) > 0)
    <div
        class="mt-2 flex flex-col space-y-2 border-l text-xs dark:border-l-white/15"
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

{{-- Sponsor us --}}
<a
    href="https://github.com/nativephp/laravel?sponsor=1"
    class="mt-5 grid w-40 place-items-center gap-2 rounded-xl bg-pink-100 px-5 pb-3 pt-3.5 text-center transition duration-300 ease-in-out hover:bg-pink-200/80 dark:bg-pink-600/10 dark:hover:bg-pink-600/15"
>
    <x-icons.love-baloons class="w-9" />
    <div class="text-sm font-medium dark:font-normal">Sponsor us on GitHub</div>
</a>

{{-- Featured sponsors --}}
<h3 class="inline-flex items-center gap-1.5 pt-3.5 text-sm opacity-50">
    {{-- Icon --}}
    <x-icons.star-circle class="size-[18px]" />

    {{-- Label --}}
    <div>Featured sponsors</div>
</h3>

{{-- List --}}
<div class="space-y-3 pt-2.5"><x-sponsors.lists.docs.featured-sponsors /></div>

{{-- Corporate sponsors --}}
<h3
    class="mt-5 inline-flex items-center gap-1.5 border-t border-t-black/20 pt-3.5 text-sm opacity-50 dark:border-t-white/15"
>
    {{-- Icon --}}
    <x-icons.briefcase class="size-[18px]" />

    {{-- Label --}}
    <div>Corporate sponsors</div>
</h3>

{{-- List --}}
<div class="space-y-3 pt-2.5"><x-sponsors.lists.docs.corporate-sponsors /></div>
