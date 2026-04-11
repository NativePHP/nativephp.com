<x-layout title="Documentation - NativePHP">
    <section class="mx-auto mt-10 max-w-3xl px-5 md:mt-20">
        <header class="mb-12 text-center">
            <h1 class="text-4xl font-bold md:text-5xl dark:text-white/90">Documentation</h1>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-600 dark:text-white/60">
                Choose your platform to get started.
            </p>
        </header>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
            {{-- Mobile --}}
            <a href="{{ route('docs.latest', ['platform' => 'mobile']) }}"
               class="group flex flex-col items-center rounded-xl bg-gray-100/80 p-10 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                    <x-icons.device-mobile-phone class="size-8" />
                </div>
                <h2 class="text-xl font-medium">Mobile</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Build native iOS and Android apps</p>
            </a>

            {{-- Desktop --}}
            <a href="{{ route('docs.latest', ['platform' => 'desktop']) }}"
               class="group flex flex-col items-center rounded-xl bg-gray-100/80 p-10 text-center transition duration-300 hover:-translate-y-1 hover:bg-gray-200/80 hover:shadow-lg dark:bg-gray-800/50 dark:hover:bg-gray-700/50 dark:hover:shadow-gray-900/30">
                <div class="mb-5 grid size-16 place-items-center rounded-full bg-white text-black ring-1 ring-black/5 transition duration-300 group-hover:rotate-3 dark:bg-gray-900 dark:text-white dark:ring-white/10">
                    <x-icons.pc class="size-8" />
                </div>
                <h2 class="text-xl font-medium">Desktop</h2>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Build native macOS, Windows and Linux apps</p>
            </a>
        </div>
    </section>
</x-layout>
