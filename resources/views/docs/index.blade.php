<x-layout>

    <main class="relative max-w-7xl mx-auto mb-64 pt-16 px-4">
        @include('docs.navigation')

        <div class="lg:pl-[20rem] lg:pt-4">
            <div class="max-w-3xl mx-auto xl:max-w-none xl:ml-0 xl:mr-[15.5rem] xl:pr-16">

                <div class="bg-red-50 border-2 border-red-500 px-6 py-4 text-red-800 text-lg mb-8 flex space-x-4 items-center">
                    <span class="text-5xl">⚠️</span>
                    <span>
                        NativePHP is currently an <em class="font-bold">alpha</em> release and is not ready for production applications
                        yet.
                    </span>
                </div>

                <div class="text-5xl font-bold tracking-tight mb-4 text-[#00aaa6]">
                    {{$title}}
                </div>

                @if (count($tableOfContents) > 0)
                    <ul class="mt-8 space-y-2">
                        @foreach($tableOfContents as $item)
                            <li class="@if($item['level'] == 2) before:content-['#']  font-medium text-gray-800 @else before:content-['##'] @endif before:text-[#00aaa6] @if($item['level'] == 3) ml-4 @endif">
                                <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="prose mt-12">
                    {!! $content !!}
                </div>
            </div>
        </div>


    </main>


</x-layout>
