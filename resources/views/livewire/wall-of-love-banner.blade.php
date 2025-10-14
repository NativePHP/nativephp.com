<div>
    @if($this->shouldShowBanner())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6" x-data x-on:banner-dismissed.window="$el.style.display='none'">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="font-medium text-blue-800 dark:text-blue-200">
                            Join our Wall of Love!
                        </h3>
                        <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                            As an early adopter who purchased a license before June 1st, 2025, we'd love to feature you on
                            our <a href="{{ route('wall-of-love') }}" target="_blank">Wall of Love page</a>.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('customer.wall-of-love.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Your Details
                            </a>
                            <button wire:click="dismissBanner" type="button" class="ml-3 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                                Maybe later
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
