<aside
    class="sticky top-20 hidden max-h-[calc(100dvh-7rem)] w-[18rem] shrink-0 overflow-x-hidden overflow-y-auto pt-4 pr-3 lg:block"
>
    <div class="relative flex flex-1 flex-col pb-5">
        <x-platform-switcher />

        <nav class="docs-navigation">{!! $slot !!}</nav>
    </div>
</aside>
