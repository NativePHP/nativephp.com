<x-layout>

    <main class="relative px-4 lg:pt-16 mx-auto max-w-7xl min-h-screen flex flex-col">
        <div class="lg:flex gap-8 flex-1 mb-64">
            @include('docs.navigation')

            <div class="lg:pt-4">
                <div class="max-w-3xl mx-auto xl:max-w-none xl:ml-0 xl:mr-[15.5rem] xl:pr-16">

                    <div class="flex items-center px-6 py-4 mb-8 space-x-4 text-lg text-red-800 border-2 border-red-500 bg-red-50 dark:text-white dark:bg-red-800">
                        <span class="text-5xl">⚠️</span>
                        <span>
                            NativePHP is currently in <em class="font-bold">alpha</em>. We do not recommend distributing
                            production releases of your applications yet.
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
