<x-layout :hasMenu="! empty($sidebarLeft)">
    <main class="mx-auto flex w-full max-w-5xl grow px-4 pt-5 xl:px-8">
        @if (! empty($sidebarLeft))
            <x-sidebar-left-navigation>
                {{ $sidebarLeft }}
            </x-sidebar-left-navigation>
        @endif

        <div class="w-full min-w-0 grow px-2 pt-2 lg:pl-5">
            @if (! empty($sidebarRight))
                <x-sidebar-right>
                    {{ $sidebarRight }}
                </x-sidebar-right>
            @endif

            {{ $slot }}
        </div>
    </main>
</x-layout>
