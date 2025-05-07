<x-layout title="Support - NativePHP">
    {{-- Support Grid Section --}}
    <section class="mx-auto mt-10 max-w-5xl px-5 md:mt-14">
        {{-- Header --}}
        <header class="mb-10 text-center">
            <h1 class="text-4xl font-bold md:text-5xl dark:text-white/90">Support</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-white/60">
                Get help with NativePHP through our various support channels.
            </p>
        </header>

        {{-- Additional Support Information --}}
        <div class="my-10 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-4 text-2xl font-medium">Need more help?</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Check out our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a> for comprehensive guides and tutorials to help you get the most out of NativePHP.
            </p>
        </div>

        {{-- Support Grid --}}
        <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-2">

            {{-- GitHub Box --}}
            <a href="https://github.com/NativePHP/laravel/issues"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Get help on GitHub">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                    <x-icons.github class="size-8" />
                </div>
                <h2 class="text-xl font-medium">GitHub</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Report issues or contribute to the project</p>
            </a>

            {{-- Discord Box --}}
            <a href="{{ $discordLink }}"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Join our Discord community">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-[#5865F2] ring-1 ring-black/5 transition duration-300 group-hover:-rotate-3 dark:bg-gray-900 dark:ring-white/10">
                    <x-icons.discord class="size-8" />
                </div>
                <h2 class="text-xl font-medium">Discord</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Join the community and get real-time help</p>
            </a>

        </div>
    </section>
</x-layout>
