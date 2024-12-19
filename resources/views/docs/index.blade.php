<x-layout>

    <main class="overflow-hidden lg:flex lg:flex-1 lg:flex-col">
        <div class="max-w-screen-xl w-full mx-auto px-3 sm:px-6 flex flex-wrap justify-between gap-8">
            @include('docs.navigation')


            <div class="overflow-hidden lg:ml-[240px] max-w-prose w-full py-8 sm:px-8">

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

                <div class="mt-8 prose dark:prose-invert">
                    {!! $content !!}
                </div>

                <x-separator class="mt-8 -mr-4 -ml-4"/>

                @php $linkAlign = $previousPage === null ? 'right' : 'between'; @endphp
                <x-flex-list-of-links align="{{$linkAlign}}" class="mt-8">
                    @if($previousPage !== null)
                        <x-link-button href="{{ $previousPage['path'] }}">
                            <span class="md:hidden">{{__('Previous page:')}} </span>
                            <span aria-hidden="true" class="hidden sm:inline">&larr;</span>
                            <span>{{ $previousPage['title'] }}</span>
                        </x-link-button>
                    @endif
                    @if($nextPage !== null)
                        <x-link-button href="{{ $nextPage['path'] }}">
                            <span class="md:hidden">{{__('Next page:')}} </span>
                            <span>{{ $nextPage['title'] }}</span>
                            <span aria-hidden="true" class="hidden sm:inline"> &rarr;</span>
                        </x-link-button>
                    @endif
                </x-flex-list-of-links>

                <x-separator class="mt-8 -mr-4 -ml-4"/>
                <div class="mt-8 text-center">
                    <a href="{{ $editUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                        {{__('Edit this page on GitHub')}}
                    </a>
                </div>
            </div>
            <div class="hidden max-h-[calc(100%-134px)] overflow-y-auto
            xl:fixed xl:right-[max(0px,calc(50%-48rem))] xl:block xl:py-8 xl:px-4 xl:pr-3 xl:w-full xl:max-w-sm">
                <x-sidebar-title>{{__('On this page')}}</x-sidebar-title>
                @if (count($tableOfContents) > 0)
                    <ul class="pl-2 space-y-2 text-sm">
                        @foreach($tableOfContents as $item)
                            <li class="hover:text-gray-400 @if($item['level'] == 2) font-medium text-gray-800 dark:text-gray-200 @endif  @if($item['level'] == 3) ml-4 @endif">
                                <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <x-sidebar-title class="mt-14">{{__('Featured sponsors')}}</x-sidebar-title>
                <div class="mt-4 flex flex-col gap-4 w-3/4 pl-3">
                    <x-sponsors-featured height="h-12" :same-height="false"/>
                </div>

                <x-sidebar-title class="mt-14">{{__('Corporate sponsors')}}</x-sidebar-title>
                <div class="mt-4 flex flex-col gap-6 w-3/4 pl-3">
                    <x-sponsors-corporate height="h-8"/>
                </div>
            </div>
        </div>

        <x-footer/>
    </main>


</x-layout>
