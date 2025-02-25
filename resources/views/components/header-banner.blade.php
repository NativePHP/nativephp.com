@props(['hasMenu' => false])
<div class="sticky top-0 z-50
text-white blur-background bg-gray-50/85 dark:bg-gray-800/85
border-b border-gray-100 dark:border-0
">

    <div class="hidden lg:block">
        <x-alert/>
    </div>

    <div class="relative flex top-0 left-0 z-50
        md:grid md:grid-cols-4
        items-center justify-between gap-6 px-6 py-3
        mx-auto max-w-8xl
        sm:py-4">
        <a
            href="/"
            class="inline-flex items-center transition rounded hover:text-white/80 focus:outline-none
                max-md:w-32
                focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-red-600"
        >
            <img src="{{ asset('logo.svg') }}" class="h-8 dark:hidden">
            <img src="{{ asset('logo-dark.svg') }}" class="hidden h-8 dark:block">
            <span class="sr-only">NativePHP</span>
        </a>

        <div class="flex-1 md:col-span-2 lg:mx-16 xl:mx-32">
            <div id="docsearch"
            x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' });"
            >

            </div>
        </div>

        <div class="hidden lg:flex items-center justify-end space-x-5">

            <a href="{{ $bskyLink }}" title="Bluesky">
                <x-icons.bluesky class="size-5 text-black dark:invert hover:text-[#00aaa6] hover:invert-0"/>
            </a>

            <a href="{{ $discordLink }}" title="Go to discord server">
                <x-icons.discord class="size-5 text-black dark:text-white hover:text-[#00aaa6]"/>
            </a>

            <a href="{{ $openCollectiveLink }}" title="NativePHP on LinkedIn">
                <x-icons.opencollective class="size-5 text-black dark:invert hover:text-[#00aaa6] hover:invert-0"/>
            </a>

            <a href="{{ $githubLink }}" title="Source code of NativePHP">
                <x-icons.github class="size-5  dark:fill-white hover:fill-[#00aaa6]"/>
            </a>
        </div>
        <div class="lg:hidden flex justify-end pl-4">
            @if($hasMenu)
                <button type="button" class="" @click="showDocsNavigation = !showDocsNavigation">
                    <div x-show="!showDocsNavigation">
                        <x-icons.menu class="w-6 h-6 text-teal-600 dark:text-teal-300"/>
                    </div>
                    <div x-show="showDocsNavigation">
                        <x-icons.close class="w-6 h-6 text-teal-600"/>
                    </div>
                </button>
            @else
                <a href="{{route('docs')}}" class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-200
                 flex items-center gap-1">
                    <x-icons.book-text class="size-5"/>
                    <span class="text-md hidden sm:inline">Documentation</span>
                </a>
            @endif
        </div>
    </div>
</div>
<div class="block lg:hidden">
    <x-alert/>
</div>
