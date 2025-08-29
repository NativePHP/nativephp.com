<x-docs-layout>
    <x-slot name="sidebarLeft">
        {!! $navigation !!}
    </x-slot>

    <x-slot name="sidebarRight">
        <x-docs.toc-and-sponsors :tableOfContents="$tableOfContents" />
    </x-slot>

    <h1 class="text-4xl font-semibold">
        {{ $title }}
    </h1>

    <x-docs.separator class="mt-4" />

    {{-- Table of contents --}}
    <div class="xl:hidden pt-5">
        {{-- Copy as Markdown Button --}}
        <x-docs.copy-markdown-button class="mt-4" />
    
        <h3 class="inline-flex items-center gap-1.5 text-sm opacity-50">
            {{-- Icon --}}
            <x-icons.stacked-lines class="size-[18px]" />
            {{-- Label --}}
            <div>On this page</div>
        </h3>
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
    </div>

    <div
        class="prose dark:prose-invert prose-headings:scroll-mt-20 prose-headings:text-gray-800 sm:prose-headings:scroll-mt-32 dark:prose-headings:text-gray-50 mt-8 max-w-none"
    >
        {!! $content !!}
    </div>

    <x-docs.separator class="mt-8" />

    @php
        $linkAlign = $previousPage === null ? 'right' : 'between';
    @endphp

    <x-docs.flex-list-of-links
        align="{{$linkAlign}}"
        class="mt-5"
    >
        @if ($previousPage !== null)
            <x-docs.link-button href="{{ $previousPage['path'] }}">
                <div class="self-center justify-self-start">
                    <div
                        class="flex items-center justify-start gap-1.5 opacity-60"
                    >
                        <x-icons.right-arrow
                            class="size-3 shrink-0 -scale-x-100"
                        />
                        <div class="text-sm">Previous</div>
                    </div>
                    <div class="pt-1">{{ $previousPage['title'] }}</div>
                </div>
            </x-docs.link-button>
        @endif

        @if ($nextPage !== null)
            <x-docs.link-button href="{{ $nextPage['path'] }}">
                <div class="self-center justify-self-end">
                    <div
                        class="flex items-center justify-end gap-1.5 opacity-60"
                    >
                        <div class="text-sm">Next</div>
                        <x-icons.right-arrow class="size-3 shrink-0" />
                    </div>
                    <div class="pt-1">{{ $nextPage['title'] }}</div>
                </div>
            </x-docs.link-button>
        @endif
    </x-docs.flex-list-of-links>

    <div class="pt-5 text-center sm:text-left">
        <x-docs.link-subtle
            href="{{ $editUrl }}"
            target="_blank"
            rel="noopener"
        >
            Edit this page on GitHub
        </x-docs.link-subtle>
    </div>
</x-docs-layout>
