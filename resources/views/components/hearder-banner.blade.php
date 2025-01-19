@props(['hasMenu' => false])
<div class="sticky top-0 z-50
text-white blur-background bg-gray-50/85 dark:bg-gray-800/85
border-b border-gray-100 dark:border-0
">

    <div class="hidden lg:block">
        <x-alert/>
    </div>
    <div class="
    relative flex top-0 left-0 z-50
    md:flex
    items-center justify-between gap-6 px-6 py-3
    mx-auto max-w-8xl
    sm:py-4">
        <a
            href="/"
            class="inline-flex items-center gap-3 transition rounded hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-red-600"
        >
            <img src="{{ asset('logo.svg') }}" class="h-8 dark:hidden">
            <img src="{{ asset('logo-dark.svg') }}" class="hidden h-8 dark:block">
            <span class="sr-only">NativePHP</span>
        </a>
        <div id="docsearch"></div>
        <a class="group
        text-sm bg-purple-500 border-purple-600
dark:border-purple-500 dark:bg-purple-700
hover:bg-purple-600 dark:hover:bg-purple-800
        px-4 py-1.5
        border rounded-md
        font-medium
        text-white
        hidden lg:block" href="{{route('early-adopter')}}">
            <div class="group-hover:animate-none animate-pulse flex items-center gap-2">
                <x-icons.party-popper class="size-4"/>
{{--                <x-icons.sparkles class="size-4"/>--}}
{{--                <x-icons.device-mobile-phone class="size-4"/>--}}
                <span>Early Adopter Program</span>
            </div>
        </a>
        <div class="hidden lg:flex items-center space-x-5">

            <a href="https://bsky.app/profile/nativephp.bsky.social" title="Bluesky">
                <x-icons.bluesky class="size-5 text-black dark:invert hover:text-[#00aaa6] hover:invert-0"/>
            </a>

            <a href="https://discord.gg/X62tWNStZK" title="Go to discord server">
                <x-icons.discord class="size-5 dark:fill-white hover:fill-[#00aaa6]"/>
            </a>

            <a href="https://opencollective.com/nativephp" title="NativePHP on LinkedIn">
                <x-icons.opencollective class="size-5 text-black dark:invert hover:text-[#00aaa6] hover:invert-0"/>
            </a>

            <a href="https://github.com/nativephp" title="Source code of NativePHP">
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
