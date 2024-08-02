<x-layout>

    <main class="relative px-4 lg:pt-16 mx-auto max-w-7xl min-h-screen flex flex-col">
        <div class="lg:flex gap-8 flex-1 mb-64">
            @include('docs.navigation')

            <div class="lg:pt-4">
                <div class="max-w-3xl mx-auto xl:max-w-none xl:ml-0 xl:mr-[15.5rem] xl:pr-16">

                    <div class="flex px-6 py-4 mb-8 space-x-4 text-lg text-orange-800 border-2 border-orange-500 bg-orange-50 dark:text-white dark:bg-orange-800">
                        <x-heroicon-o-shield-exclamation class="size-10" />
                        <span>
                            NativePHP is currently
                            <a href="/docs/getting-started/status" class="font-bold italic font-mono flex-inline px-2 text-base bg-orange-200 rounded">alpha</a>.
                            <br><br>
                            <a href="https://github.com/nativephp/laravel?sponsor=1" class="font-bold inline-flex items-center bg-orange-200 rounded-md px-3 py-1 border border-orange-400 hover:bg-orange-300 group" target="_blank">
                                Let's get to beta!
                                <x-heroicon-o-rocket-launch class="ml-2 size-5 group-hover:hidden" />
                                <x-heroicon-s-rocket-launch class="hidden ml-2 size-5 group-hover:block" />
                            </a>
                        </span>
                    </div>

                    <div class="text-5xl font-bold tracking-tight mb-4 text-[#00aaa6]">
                        {{$title}}
                    </div>

                    @if (count($tableOfContents) > 0)
                        <ul class="mt-8 space-y-2">
                            @foreach($tableOfContents as $item)
                                <li class="@if($item['level'] == 2) before:content-['#']  font-medium text-gray-800 dark:text-gray-200 @else before:content-['##'] @endif before:text-[#00aaa6] @if($item['level'] == 3) ml-4 @endif">
                                    <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-12 prose dark:prose-invert">
                        {!! $content !!}
                    </div>
                </div>
            </div>
        </div>

        <x-footer />
    </main>


</x-layout>
