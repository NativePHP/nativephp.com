<x-layout-three-columns>
    <x-slot name="sidebarLeft">
        {!! $navigation !!}
    </x-slot>

    <x-slot name="sidebarRight">
        <x-toc-and-sponsors :tableOfContents="$tableOfContents"/>
    </x-slot>

    <h1 class="text-4xl font-semibold mb-4 text-[#00aaa6]">
        {{$title}}
    </h1>

    <x-separator class="mt-3 -mr-4 -ml-4"/>

    <x-alert-beta/>

    @if (count($tableOfContents) > 0)
        <ul class="mt-8 space-y-2 block xl:hidden">
            @foreach($tableOfContents as $item)
                <li class="@if($item['level'] == 2) font-bold text-gray-800 dark:text-gray-200 @else before:content-['→'] @endif  before:text-[#00aaa6] @if($item['level'] == 3) ml-6 @endif">
                    <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="mt-8 prose dark:prose-invert prose-headings:scroll-mt-20 sm:prose-headings:scroll-mt-32 prose-headings:text-gray-800 dark:prose-headings:text-gray-50">
        {!! $content !!}
    </div>

    <x-separator class="mt-8 -mr-4 -ml-4"/>

    @php $linkAlign = $previousPage === null ? 'right' : 'between'; @endphp
    <x-flex-list-of-links align="{{$linkAlign}}" class="mt-8">
        @if($previousPage !== null)
            <x-link-button href="{{ $previousPage['path'] }}">
                <span class="md:hidden">Previous page: </span>
                <span aria-hidden="true" class="hidden sm:inline">&larr;</span>
                <span>{{ $previousPage['title'] }}</span>
            </x-link-button>
        @endif
        @if($nextPage !== null)
            <x-link-button href="{{ $nextPage['path'] }}">
                <span class="md:hidden">Next page: </span>
                <span>{{ $nextPage['title'] }}</span>
                <span aria-hidden="true" class="hidden sm:inline"> &rarr;</span>
            </x-link-button>
        @endif
    </x-flex-list-of-links>

    <x-separator class="mt-8 -mr-4 -ml-4"/>

    <div class="mt-4 text-center">
        <x-link-subtle href="{{ $editUrl }}" target="_blank" rel="noopener">
            Edit this page on GitHub
        </x-link-subtle>
    </div>

</x-layout-three-columns>
