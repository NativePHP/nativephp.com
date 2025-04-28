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

            {{-- Email Box --}}
            <a href="mailto:support@nativephp.com"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Contact us via email">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:ring-white/10">
                    <x-icons.email-document class="size-8" />
                </div>
                <h2 class="text-xl font-medium">Email</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Get in touch with our support team</p>
            </a>

            {{-- Support Tickets Box --}}
            <a href="https://support.nativephp.com"
               target="_blank"
               rel="noopener"
               class="group flex w-full flex-col items-center rounded-xl bg-gray-100/80 p-8 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30"
               aria-label="Submit a support ticket">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-violet-500 ring-1 ring-black/5 transition duration-300 group-hover:-rotate-3 dark:bg-gray-900 dark:ring-white/10">
                    <svg class="size-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-medium">Support Tickets</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Submit a detailed help request</p>
            </a>

        </div>

        {{-- Additional Support Information --}}
        <div class="mt-20 rounded-xl bg-gradient-to-br from-[#FFF0DC] to-[#E8EEFF] p-8 dark:from-blue-900/10 dark:to-[#4c407f]/25">
            <h2 class="mb-4 text-2xl font-medium">Need more help?</h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Check out our <a href="/docs" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300">documentation</a> for comprehensive guides and tutorials to help you get the most out of NativePHP.
            </p>
        </div>
    </section>
</x-layout>
