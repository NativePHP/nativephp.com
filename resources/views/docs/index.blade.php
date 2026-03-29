<x-docs-layout>
    <x-slot name="sidebarLeft">
        {!! $navigation !!}
    </x-slot>

    <x-slot name="sidebarRight">
        {{-- Version switcher --}}
        @if($platform === 'desktop')
            <livewire:version-switcher :versions="[
                1 => '1.x',
                2 => '2.x'
            ]" />
        @elseif($platform === 'mobile')
            <livewire:version-switcher :versions="[
                1 => '1.x',
                2 => '2.x',
                3 => '3.x'
            ]" />
        @endif

        <x-docs.toc-and-sponsors :tableOfContents="$tableOfContents" />
    </x-slot>

    <x-docs.old-version-notice
        :platform="$platform"
        :version="$version"
        :page="request()->route('page')"
    />

    <h1 class="text-4xl font-semibold">
        {{ $title }}
    </h1>

    <x-docs.separator class="mt-4" />

    {{-- Table of contents --}}
    <div class="xl:hidden pt-9 space-y-4">

        {{-- Version switcher --}}
        @if($platform === 'desktop')
            <livewire:version-switcher :versions="[
                1 => '1.x',
                2 => '2.x'
            ]" />
        @elseif($platform === 'mobile')
            <livewire:version-switcher :versions="[
                1 => '1.x',
                2 => '2.x',
                3 => '3.x'
            ]" />
        @endif

        {{-- Copy as Markdown Button --}}
        <x-docs.copy-markdown-button />

    </div>

    @if (count($tableOfContents) > 0)
        <div class="sticky top-20 z-10 mt-8 mb-4 flex justify-end">
            <div class="rounded-full bg-white shadow-sm dark:bg-zinc-800">
                <flux:dropdown position="bottom" align="end">
                    <flux:button variant="filled" size="sm" class="!rounded-full">
                        <x-icons.stacked-lines class="size-4" />
                        On this page
                    </flux:button>

                    <flux:popover class="w-64">
                        <nav class="flex max-h-80 flex-col gap-0.5 overflow-y-auto">
                            @foreach ($tableOfContents as $item)
                                <a
                                    href="#{{ $item['anchor'] }}"
                                    x-on:click.prevent="document.getElementById('{{ $item['anchor'] }}')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                                    @class([
                                        'rounded-md px-2 py-1.5 text-xs transition hover:bg-zinc-100 dark:text-white/80 dark:hover:bg-zinc-700',
                                        'pl-2' => $item['level'] == 2,
                                        'pl-5' => $item['level'] == 3,
                                    ])
                                >
                                    {{ $item['title'] }}
                                </a>
                            @endforeach
                        </nav>
                    </flux:popover>
                </flux:dropdown>
            </div>
        </div>
    @endif

    <div
        class="prose dark:prose-invert prose-headings:scroll-mt-20 prose-headings:text-gray-800 sm:prose-headings:scroll-mt-32 dark:prose-headings:text-gray-50 max-w-none"
    >
        {!! $content !!}
    </div>

    <x-docs.separator class="mt-8" />

    @php
        $linkAlign = $previousPage === null ? 'right' : 'between';
    @endphp

    <x-docs.flex-list-of-links
        align="{{$linkAlign}}"
        class="mt-5"
    >
        @if ($previousPage !== null)
            <x-docs.link-button href="{{ $previousPage['path'] }}">
                <div class="self-center justify-self-start">
                    <div
                        class="flex items-center justify-start gap-1.5 opacity-60"
                    >
                        <x-icons.right-arrow
                            class="size-3 shrink-0 -scale-x-100"
                        />
                        <div class="text-sm">Previous</div>
                    </div>
                    <div class="pt-1">{{ $previousPage['title'] }}</div>
                </div>
            </x-docs.link-button>
        @endif

        @if ($nextPage !== null)
            <x-docs.link-button href="{{ $nextPage['path'] }}">
                <div class="self-center justify-self-end">
                    <div
                        class="flex items-center justify-end gap-1.5 opacity-60"
                    >
                        <div class="text-sm">Next</div>
                        <x-icons.right-arrow class="size-3 shrink-0" />
                    </div>
                    <div class="pt-1">{{ $nextPage['title'] }}</div>
                </div>
            </x-docs.link-button>
        @endif
    </x-docs.flex-list-of-links>

    <div class="pt-5 text-center sm:text-left">
        <x-docs.link-subtle
            href="{{ $editUrl }}"
            target="_blank"
            rel="noopener"
        >
            Edit this page on GitHub
        </x-docs.link-subtle>
    </div>
</x-docs-layout>
