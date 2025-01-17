<div class="
    hidden xl:block
    fixed z-20
{{--  With banner: 6.5rem  --}}
{{--  Without banner: 4.3rem  --}}
    top-[6.5rem] bottom-0 right-[max(0px,calc(50%-45rem))]
    w-[19.5rem]
    pt-6 pb-10
    px-8
    overflow-y-auto

    border-l border-[#00aaa6] border-opacity-10 dark:border-gray-800
">
    <x-sidebar-title>On this page</x-sidebar-title>
    @if (count($tableOfContents) > 0)
        <ul class="pl-2 space-y-2 text-sm">
            @foreach($tableOfContents as $item)
                <li class="hover:text-gray-400 @if($item['level'] == 2) font-medium text-gray-800 dark:text-gray-200 @endif  @if($item['level'] == 3) ml-4 @endif">
                    <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif

    <x-sidebar-title class="mt-14">Featured sponsors</x-sidebar-title>
    <div class="mt-4 flex flex-col gap-4 w-3/4 pl-3">
        <x-sponsors-featured height="h-12" :same-height="false"/>
    </div>

    <x-sidebar-title class="mt-14">Corporate sponsors</x-sidebar-title>
    <div class="mt-4 flex flex-col gap-6 w-3/4 pl-3">
        <x-sponsors-corporate height="h-8"/>
    </div>
</div>
