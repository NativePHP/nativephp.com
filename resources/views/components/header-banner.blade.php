@props(['hasMenu' => false])
<div
    class="sticky top-0 z-50 border-b border-gray-100 bg-gray-50/85 text-white dark:border-0 dark:bg-gray-800/85"
>
    <div class="hidden lg:block">
        <x-eap-banner />
    </div>

    <div
        class="max-w-8xl relative left-0 top-0 z-50 mx-auto flex items-center justify-between gap-6 px-6 py-3 sm:py-4 md:grid md:grid-cols-4"
    >
        <a
            href="/"
            class="inline-flex items-center rounded transition hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/80 focus-visible:ring-offset-2 focus-visible:ring-offset-red-600 max-md:w-32"
        >
            <img
                src="{{ asset('logo.svg') }}"
                class="h-8 dark:hidden"
            />
            <img
                src="{{ asset('logo-dark.svg') }}"
                class="hidden h-8 dark:block"
            />
            <span class="sr-only">NativePHP</span>
        </a>

        <div class="flex-1 md:col-span-2 lg:mx-16 xl:mx-32">
            <div
                id="docsearch"
                x-on:click="if (window.innerWidth < 640) window.scrollTo({ top: 0, behavior: 'instant' })"
            ></div>
        </div>

        <div class="hidden items-center justify-end space-x-5 lg:flex">
            <a
                href="{{ $bskyLink }}"
                title="Bluesky"
            >
                <x-icons.bluesky
                    class="size-5 text-black hover:text-[#00aaa6] hover:invert-0 dark:invert"
                />
            </a>

            <a
                href="{{ $discordLink }}"
                title="Go to discord server"
            >
                <x-icons.discord
                    class="size-5 text-black hover:text-[#00aaa6] dark:text-white"
                />
            </a>

            <a
                href="{{ $openCollectiveLink }}"
                title="NativePHP on LinkedIn"
            >
                <x-icons.opencollective
                    class="size-5 text-black hover:text-[#00aaa6] hover:invert-0 dark:invert"
                />
            </a>

            <a
                href="{{ $githubLink }}"
                title="Source code of NativePHP"
            >
                <x-icons.github
                    class="size-5 hover:fill-[#00aaa6] dark:fill-white"
                />
            </a>
        </div>
        <div class="flex justify-end pl-4 lg:hidden">
            @if ($hasMenu)
                <button
                    type="button"
                    class=""
                    @click="showDocsNavigation = !showDocsNavigation"
                >
                    <div x-show="!showDocsNavigation">
                        <x-icons.menu
                            class="h-6 w-6 text-teal-600 dark:text-teal-300"
                        />
                    </div>
                    <div x-show="showDocsNavigation">
                        <x-icons.close class="h-6 w-6 text-teal-600" />
                    </div>
                </button>
            @else
                <a
                    href="{{ route('docs') }}"
                    class="flex items-center gap-1 text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-200"
                >
                    <x-icons.book-text class="size-5" />
                    <span class="text-md hidden sm:inline">Documentation</span>
                </a>
            @endif
        </div>
    </div>
</div>
<div class="block lg:hidden">
    <x-eap-banner />
</div>
