<div class="
fixed -mx-3 overflow-y-auto max-h-[calc(100%-134px)] hidden lg:block py-8 lg:max-w-[240px] lg:w-full
 border-r border-[#00aaa6] border-opacity-10 dark:border-0
 dark:text-gray-200
  pl-4
 ">
    <nav class="flex flex-col flex-1">
        {!! $navigation !!}
    </nav>
</div>

{{--<div class="my-8 lg:hidden" x-data="{ showDocsNavigation: false }">--}}
{{--    <div class="flex justify-end">--}}
{{--        <button type="button" class="p-4" @click="showDocsNavigation = !showDocsNavigation">--}}
{{--            <div x-show="!showDocsNavigation">--}}
{{--                <x-icons.menu class="w-6 h-6 text-teal-600 dark:text-red-300"/>--}}
{{--            </div>--}}
{{--            <div x-show="showDocsNavigation">--}}
{{--                <x-icons.close class="w-6 h-6 text-teal-600"/>--}}
{{--            </div>--}}
{{--        </button>--}}
{{--    </div>--}}

{{--    <nav x-show="showDocsNavigation" x-transition:enter="transition ease-out duration-200"--}}
{{--         x-transition:enter-start="translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"--}}
{{--         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-y-0 opacity-100"--}}
{{--         x-transition:leave-end="translate-y-1 opacity-0"--}}
{{--         class="mt-2 px-4 pt-4 border-b border-[#00aaa6] border-opacity-50 dark:border-opacity-90">--}}
{{--        {!! $navigation !!}--}}
{{--    </nav>--}}
{{--</div>--}}
