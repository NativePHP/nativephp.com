<x-layout title="Page Not Found">
    <div class="mx-auto max-w-5xl px-5 py-20 xl:max-w-7xl 2xl:max-w-360">
        <div class="flex flex-col items-center justify-center text-center">
            {{-- Error illustration or icon --}}
            <div class="mb-8">
                <div class="flex h-32 w-32 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <svg class="h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
            </div>

            {{-- Error message --}}
            <h1 class="mb-4 text-4xl font-bold text-gray-900 dark:text-white">
                Page Not Found
            </h1>

            <p class="mb-8 max-w-md text-lg text-gray-600 dark:text-gray-400">
                The page you're looking for doesn't exist. It may have been moved, deleted, or you entered the wrong URL.
            </p>

            {{-- Action buttons --}}
            <div class="flex flex-col gap-4 sm:flex-row">
                <a
                    href="{{ route('welcome') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-black px-6 py-3 text-sm font-medium text-white transition duration-200 hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Go to Homepage
                </a>

                <a
                    href="{{ route('blog') }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 transition duration-200 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Read our Blog
                </a>
            </div>
        </div>
    </div>
</x-layout>
