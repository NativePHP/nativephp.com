<x-layout-three-columns>
    <x-slot name="sidebarLeft">
        {!! $navigation !!}
    </x-slot>

    <x-slot name="sidebarRight">
        <x-toc-and-sponsors :tableOfContents="$tableOfContents" />
    </x-slot>

    <h1 class="mb-4 text-4xl font-semibold text-[#00aaa6]">
        {{ $title }}
    </h1>

    <x-separator class="-ml-4 -mr-4 mt-3" />

    <x-alert-beta />

    @if (count($tableOfContents) > 0)
        <ul class="mt-8 block space-y-2 xl:hidden">
            @foreach ($tableOfContents as $item)
                <li
                    class="@if($item['level'] == 2) font-bold text-gray-800 dark:text-gray-200 @else before:content-['→'] @endif @if($item['level'] == 3) ml-6 @endif before:text-[#00aaa6]"
                >
                    <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif

    <div
        class="prose mt-8 dark:prose-invert prose-headings:scroll-mt-20 prose-headings:text-gray-800 sm:prose-headings:scroll-mt-32 dark:prose-headings:text-gray-50"
    >
        {!! $content !!}
    </div>

    <x-separator class="-ml-4 -mr-4 mt-8" />

    @php
        $linkAlign = $previousPage === null ? 'right' : 'between';
    @endphp

    <x-flex-list-of-links
        align="{{$linkAlign}}"
        class="mt-8"
    >
        @if ($previousPage !== null)
            <x-link-button href="{{ $previousPage['path'] }}">
                <div class="self-center justify-self-start">
                    <div
                        class="flex items-center justify-start gap-1.5 text-gray-500"
                    >
                        <x-icons.right-arrow class="size-3 -scale-x-100" />
                        <div class="text-sm">Previous</div>
                    </div>
                    <div class="pt-1">{{ $previousPage['title'] }}</div>
                </div>
            </x-link-button>
        @endif

        @if ($nextPage !== null)
            <x-link-button href="{{ $nextPage['path'] }}">
                <div class="self-center justify-self-end">
                    <div
                        class="flex items-center justify-end gap-1.5 text-gray-500"
                    >
                        <div class="text-sm">Next</div>
                        <x-icons.right-arrow class="size-3" />
                    </div>
                    <div class="pt-1">{{ $nextPage['title'] }}</div>
                </div>
            </x-link-button>
        @endif
    </x-flex-list-of-links>

    <div class="pt-10">
        <x-link-subtle
            href="{{ $editUrl }}"
            target="_blank"
            rel="noopener"
        >
            Edit this page on GitHub
        </x-link-subtle>
    </div>
</x-layout-three-columns>
