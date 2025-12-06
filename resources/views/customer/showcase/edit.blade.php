<x-layout title="Edit Submission">
    <div class="min-h-screen">
        {{-- Header --}}
        <header>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="py-6">
                    {{-- Breadcrumb --}}
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2">
                            <li>
                                <a href="{{ route('customer.showcase.index') }}" class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="sr-only">Back to Submissions</span>
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Edit: {{ $showcase->title }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Your Submission</h1>
                            <p class="mt-2 text-gray-600 dark:text-gray-400">
                                Update the details of your showcase submission.
                            </p>
                        </div>
                        <div>
                            @if($showcase->isApproved())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Pending Review
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <div class="max-w-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="px-6 py-8">
                    {{-- Submission Form --}}
                    <livewire:showcase-submission-form :showcase="$showcase" />
                </div>
            </div>
        </div>
    </div>
</x-layout>
