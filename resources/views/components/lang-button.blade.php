@props(['availableLangs'])

<div class="relative inline-block text-left" x-data="{ showLangDropdown: false }" @click.away="showLangDropdown = false">
    <div class="flex items-center">
        <button @click="showLangDropdown = !showLangDropdown" type="button" class="size-5 text-black dark:text-white hover:text-[#00aaa6]" id="menu-button" aria-expanded="true" aria-haspopup="true">
            <x-icons.lang />
        </button>
    </div>
    <div x-show="showLangDropdown" class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-gray-50/85 border-b border-gray-100 z-50 dark:bg-gray-700/85 dark:border-0 shadow-lg ring-1 ring-black/5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        @foreach($availableLangs as $key => $value)
            <div class="py-1" role="none">
                <form action="{{route('lang', ['lang' => $key])}}" method="post">
                    @csrf
                    <button type="submit" class="block px-4 py-2 text-sm text-black dark:text-white hover:text-[#00aaa6]" role="menuitem" tabindex="-1">{{ $value }}</a>
                </form>
            </div>
        @endforeach
    </div>
</div>