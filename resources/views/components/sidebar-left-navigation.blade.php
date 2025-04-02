<aside
    class="sticky top-20 hidden max-h-[calc(100dvh-7rem)] w-[18rem] shrink-0 overflow-y-auto overflow-x-hidden pr-3 pt-4 lg:block"
>
    <div class="relative flex flex-1 flex-col pb-5">
        <x-platform-switcher />

        <nav class="docs-navigation">{!! $slot !!}</nav>
    </div>
</aside>

<div
    x-show="showDocsNavigation"
    x-transition:enter="transition duration-200 ease-out"
    x-transition:enter-start="translate-y-1 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-1 opacity-0"
    class="fixed inset-0 z-40 h-screen w-screen overflow-y-auto overflow-x-hidden bg-white dark:bg-mirage"
>
    <div class="px-3 pt-24">
        <x-platform-switcher />

        <nav class="docs-navigation">{!! $slot !!}</nav>

        <div class="my-16 flex items-center justify-center space-x-6">
            <x-social-networks-all />
        </div>
    </div>
</div>
