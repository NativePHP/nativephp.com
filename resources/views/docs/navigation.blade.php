<div
    class="hidden lg:block absolute z-20 w-[18rem] inset-0 top-24 left-[max(0px,calc(50%-45rem))] right-auto ">

    <div class="w-full pt-8 pb-12 pl-4 border-r border-[#00aaa6] border-opacity-10 dark:border-opacity-90">

        <nav class="flex flex-col flex-1">
            {!! $navigation !!}
        </nav>

    </div>
</div>

<div class="my-8 lg:hidden" x-data="{ showDocsNavigation: false }">
    <div class="flex justify-end">
        <button type="button"
                class="p-4"
                @click="showDocsNavigation = !showDocsNavigation"
        >
            <div x-show="!showDocsNavigation">
                <x-icons.menu class="w-6 h-6 text-red-600 dark:text-red-300"/>
            </div>
            <div x-show="showDocsNavigation">
                <x-icons.close class="w-6 h-6"/>
            </div>
        </button>
    </div>

    <nav x-show="showDocsNavigation"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-y-1 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-1 opacity-0"
         class="px-4 pt-4 mt-2 border-b border-red-600 border-opacity-50 dark:border-red-300"
    >
        {!!  $navigation !!}
    </nav>

</div>
