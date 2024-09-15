<div class="
hidden lg:fixed lg:block -mx-3 overflow-y-auto max-h-[calc(100%-134px)] py-8 lg:max-w-[240px] lg:w-full
 border-r border-[#00aaa6] border-opacity-10 dark:border-0
 dark:text-gray-200
  pl-4
 ">
    <nav class="flex flex-col flex-1">
        {!! $navigation !!}
    </nav>
</div>


<nav x-show="showDocsNavigation" x-cloak=""
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="translate-y-1 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-1 opacity-0"
     class="fixed top-12 left-0 z-40 w-full max-h-screen pb-36 overflow-y-auto bg-white dark:bg-gray-700 mt-2 px-4 pt-4 border-b border-[#00aaa6] border-opacity-50 dark:border-opacity-90">
    {!! $navigation !!}
</nav>
