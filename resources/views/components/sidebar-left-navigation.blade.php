<div
    class="{{-- With banner: 6.5rem --}} {{-- Without banner: 4.3rem --}} fixed inset-0 left-[max(0px,calc(50%-45rem))] right-auto top-[6.5rem] z-20 hidden w-[19rem] overflow-y-auto border-r border-[#00aaa6] border-opacity-10 pb-10 pl-8 pr-6 pt-6 lg:block dark:border-gray-800 dark:text-gray-200"
>
    <nav class="relative flex flex-1 flex-col">
        <x-platform-switcher />

        {!! $slot !!}
    </nav>
</div>

<nav
    x-show="showDocsNavigation"
    x-transition:enter="transition duration-200 ease-out"
    x-transition:enter-start="translate-y-1 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-1 opacity-0"
    class="fixed left-0 top-20 z-40 h-screen w-full overflow-y-auto border-b border-[#00aaa6] border-opacity-50 bg-white px-4 pb-16 pt-10 dark:border-opacity-90 dark:bg-gray-700"
>
    <x-platform-switcher />

    {!! $slot !!}

    <div class="my-16 flex items-center justify-center space-x-6">
        <x-social-networks-all />
    </div>
</nav>
