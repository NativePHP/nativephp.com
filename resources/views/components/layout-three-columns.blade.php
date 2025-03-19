<x-layout :hasMenu="! empty($sidebarLeft)">
    {{-- Main container --}}
    <main
        class="2xl:max-w-8xl mx-auto flex w-full max-w-5xl grow px-4 pt-5 xl:max-w-7xl"
    >
        {{-- Left sidebar --}}
        @if (! empty($sidebarLeft))
            <x-sidebar-left-navigation>
                {{ $sidebarLeft }}
            </x-sidebar-left-navigation>
        @endif

        <div class="flex w-full min-w-0 grow px-2 pt-2 lg:pl-5">
            {{-- Content --}}
            <article class="flex w-full min-w-0 grow flex-col">
                {{ $slot }}
            </article>

            {{-- Right sidebar --}}
            @if (! empty($sidebarRight))
                <x-sidebar-right>
                    {{ $sidebarRight }}
                </x-sidebar-right>
            @endif
        </div>
    </main>
</x-layout>
