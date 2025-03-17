<x-layout :hasMenu="! empty($sidebarLeft)">
    <main class="overflow-hidden lg:flex lg:flex-1 lg:flex-col">
        <div class="max-w-8xl mx-auto px-4 sm:px-6 md:px-8">
            <main class="overflow-hidden lg:flex lg:flex-1 lg:flex-col">
                @if (! empty($sidebarLeft))
                    <x-sidebar-left-navigation>
                        {{ $sidebarLeft }}
                    </x-sidebar-left-navigation>
                @endif

                <div class="lg:pl-[19.5rem]">
                    <div
                        class="mx-auto max-w-3xl pt-4 sm:pt-6 xl:ml-0 xl:mr-[15.5rem] xl:max-w-none xl:pr-16"
                    >
                        @if (! empty($sidebarRight))
                            <x-sidebar-right>
                                {{ $sidebarRight }}
                            </x-sidebar-right>
                        @endif

                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </main>
</x-layout>
