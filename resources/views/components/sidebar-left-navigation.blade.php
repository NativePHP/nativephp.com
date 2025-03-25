<aside
    class="sticky top-20 hidden max-h-[calc(100dvh-8rem)] w-[18rem] shrink-0 overflow-y-auto overflow-x-hidden pr-3 pt-4 lg:block"
>
    <nav class="relative flex flex-1 flex-col pb-5">
        <x-platform-switcher />

        {!! $slot !!}
    </nav>
</aside>

<nav
    x-show="showDocsNavigation"
    x-transition:enter="transition duration-200 ease-out"
    x-transition:enter-start="translate-y-1 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-1 opacity-0"
    class="fixed left-0 top-20 z-40 h-screen w-full overflow-y-auto bg-white px-4 pb-16 pt-10 dark:bg-mirage"
>
    <x-platform-switcher />

    {!! $slot !!}

    <div class="my-16 flex items-center justify-center space-x-6">
        <x-social-networks-all />
    </div>
</nav>
