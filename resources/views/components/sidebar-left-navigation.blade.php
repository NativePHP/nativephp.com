<div class="
    hidden lg:block
    fixed z-20 inset-0
{{--  With banner: 6.5rem  --}}
{{--  Without banner: 4.3rem  --}}
    top-[6.5rem] left-[max(0px,calc(50%-45rem))] right-auto
    w-[19rem]
    pt-6 pb-10
    pl-8 pr-6
    overflow-y-auto
    border-r border-[#00aaa6] border-opacity-10 dark:border-gray-800
    dark:text-gray-200
 ">
    <nav class="flex flex-col flex-1 relative">
        <x-platform-switcher/>

        {!! $slot !!}
    </nav>
</div>


<nav x-show="showDocsNavigation" x-cloak=""
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="translate-y-1 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-1 opacity-0"
     class="fixed top-12 left-0 z-40 w-full h-screen pt-10 pb-16 overflow-y-auto bg-white dark:bg-gray-700 mt-2 px-4 border-b border-[#00aaa6] border-opacity-50 dark:border-opacity-90"
>

    <x-platform-switcher/>


    {!! $slot !!}


    <div class="my-16 flex items-center justify-center space-x-6">
        <x-social-networks-all/>
    </div>
</nav>
