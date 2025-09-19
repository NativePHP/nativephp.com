<x-layout title="Join our Wall of Love">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    {{-- Breadcrumb --}}
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('customer.licenses') }}" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Back to Licenses</span>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Wall of Love</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Join our Wall of Love! 💙</h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            As an early adopter, your story matters. Share your experience with NativePHP and inspire other developers in the community.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="px-6 py-8">
                    {{-- Success Message --}}
                    @if(session()->has('success'))
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Info about early adopter status --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <div>
                                <h3 class="text-base font-medium text-blue-800 dark:text-blue-200">
                                    You're an Early Adopter!
                                </h3>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                    Thank you for supporting NativePHP from the beginning. As a reward, you can appear
                                    permanently on our
                                    <a href="{{ route('wall-of-love') }}" target="_blank" class="underline">Wall of Love</a>.
                                </p>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                    Your submission will be reviewed by our team and, once approved, will appear
                                    on the page.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Submission Form --}}
                    <livewire:wall-of-love-submission-form />
                </div>
            </div>
        </div>
    </div>
</x-layout>
