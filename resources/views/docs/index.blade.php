<x-layout>

    <main class="overflow-hidden lg:flex lg:flex-1 lg:flex-col">
        <div class="max-w-screen-xl w-full mx-auto px-6 flex flex-wrap justify-between gap-8">
            @include('docs.navigation')

            <div class="overflow-hidden lg:ml-[240px] max-w-prose w-full p-8">

                <div class="text-5xl font-bold tracking-tight mb-4 text-[#00aaa6]">
                    {{$title}}
                </div>

                <div class="rounded-lg flex p-3 mt-8 space-x-4 text-orange-800 border border-orange-300 bg-orange-50 dark:text-white dark:bg-orange-800">
                    <x-heroicon-o-shield-exclamation class="size-10"/>
                    <div>
                        <p>
                            NativePHP is currently in
                            <a href="/docs/getting-started/status" class="font-bold italic font-mono flex-inline px-1 py-0.5 text-base bg-orange-200 rounded">alpha</a>
                            development
                        </p>

                        <a href="https://github.com/nativephp/laravel?sponsor=1"
                           onclick="fathom.trackEvent('beta_interest');"
                           class="mt-4 font-bold inline-flex items-center bg-orange-200 rounded-md px-3 py-1 border border-orange-400 hover:bg-orange-300 group" target="_blank">
                            Let's get to beta!
                            <x-heroicon-o-rocket-launch class="ml-2 size-5 group-hover:hidden"/>
                            <x-heroicon-s-rocket-launch class="hidden ml-2 size-5 group-hover:block"/>
                        </a>
                    </div>
                </div>

                @if (count($tableOfContents) > 0)
                    <ul class="mt-8 space-y-2 block xl:hidden">
                        @foreach($tableOfContents as $item)
                            <li class="@if($item['level'] == 2) before:content-['#']  font-medium text-gray-800 dark:text-gray-200 @else before:content-['##'] @endif before:text-[#00aaa6] @if($item['level'] == 3) ml-4 @endif">
                                <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-8 prose dark:prose-invert">
                    {!! $content !!}
                </div>
            </div>
            <div class="hidden max-h-[calc(100%-134px)] overflow-y-auto
            xl:fixed xl:right-[max(0px,calc(50%-48rem))] xl:block xl:py-8 xl:px-4 xl:pr-3 xl:w-full xl:max-w-sm">
                <div class="uppercase font-bold tracking-tight mb-4 text-gray-500">
                    On this page
                </div>
                @if (count($tableOfContents) > 0)
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach($tableOfContents as $item)
                            <li class="@if($item['level'] == 2) font-medium text-gray-800 dark:text-gray-200 @endif  @if($item['level'] == 3) ml-4 @endif">
                                <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <x-footer/>
    </main>


</x-layout>
