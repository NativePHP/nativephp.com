<x-layout>

    <main class="overflow-hidden lg:flex lg:flex-1 lg:flex-col">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
            <x-sidebar-left :navigation="$navigation"/>


            <div class="
            lg:pl-[19.5rem]
{{--            overflow-hidden lg:ml-[240px] max-w-prose w-full py-8 sm:px-8--}}
            ">
                <div class="max-w-3xl mx-auto pt-10 xl:max-w-none xl:ml-0 xl:mr-[15.5rem] xl:pr-16">

                    <x-sidebar-right :tableOfContents="$tableOfContents"/>

                    <div class="text-5xl font-bold tracking-tight mb-4 text-[#00aaa6]">
                        {{$title}}
                    </div>

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

                    <div class="mt-8 prose dark:prose-invert prose-headings:text-gray-800 dark:prose-headings:text-gray-50">
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
                    <div class="mt-8 text-center">
                        <a href="{{ $editUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            Edit this page on GitHub
                        </a>
                    </div>
                </div>
            </div>

            <x-footer/>
        </div>
    </main>


</x-layout>
