<x-sidebar-title>On this page</x-sidebar-title>
@if (count($tableOfContents) > 0)
    <ul class="mt-4 space-y-2 text-sm">
        @foreach ($tableOfContents as $item)
            <li
                @class([
                    'text-gray-700 hover:text-[#00aaa6] dark:text-gray-300',
                    'font-semibold leading-6' => $item['level'] == 2,
                    'ml-4 pb-0.5 leading-4' => $item['level'] == 3,
                    'pb-2' =>
                        $item['level'] == 3 &&
                        ($tableOfContents[$loop->index + 1]['level'] ?? 0) == 2,
                ])
            >
                <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
            </li>
        @endforeach
    </ul>
@endif

<x-sidebar-title class="mt-8">Featured sponsors</x-sidebar-title>
<div class="mt-4 flex w-3/4 flex-col gap-4 pl-3">
    <x-sponsors.lists.docs.featured-sponsors />
</div>

<x-sidebar-title class="mt-8">Corporate sponsors</x-sidebar-title>
<div class="mt-4 flex w-3/4 flex-col gap-6 pl-3">
    <x-sponsors.lists.docs.corporate-sponsors />
</div>
