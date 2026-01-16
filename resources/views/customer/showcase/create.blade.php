<x-layout title="Submit Your App">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    <a href="{{ route('dashboard') }}" class="mb-4 inline-flex items-center space-x-2 text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                        <svg class="size-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Submit Your App to the Showcase</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Share your NativePHP app with the community! Your submission will be reviewed by our team.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="px-6 py-8">
                    {{-- Info Box --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div>
                                <h3 class="text-base font-medium text-blue-800 dark:text-blue-200">
                                    Showcase Guidelines
                                </h3>
                                <ul class="mt-2 text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                                    <li>Your app must be built with NativePHP</li>
                                    <li>Include clear screenshots showcasing your app</li>
                                    <li>Provide download links or store URLs where users can get your app</li>
                                    <li>Submissions are reviewed before being published</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Submission Form --}}
                    <livewire:showcase-submission-form />
                </div>
            </div>
        </div>
    </div>
</x-layout>
