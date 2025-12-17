<form wire:submit="submit" class="space-y-6">
    {{-- Warning for approved submissions being edited --}}
    @if ($isEditing && $showcase?->isApproved())
        <div class="rounded-md bg-yellow-50 dark:bg-yellow-900/20 p-4">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Re-review Required</h3>
                    <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                        This submission is currently approved. If you make changes, it will need to be reviewed again before appearing in the showcase.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Title Field --}}
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            App Name *
        </label>
        <input wire:model="title" type="text" id="title" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        @error('title') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Description Field --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Description *
        </label>
        <textarea wire:model="description" id="description" rows="4" placeholder="Tell us about your app and what it does..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
        @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maximum 2000 characters.</p>
    </div>

    {{-- Main Image Field --}}
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            App Icon / Main Image
        </label>
        @if ($existingImage)
            <div class="mt-2 flex items-center gap-4">
                <img src="{{ Storage::disk('public')->url($existingImage) }}" alt="Current image" class="h-20 w-20 object-cover rounded-lg">
                <button type="button" wire:click="removeExistingImage" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                    Remove
                </button>
            </div>
        @endif
        <div class="mt-2">
            <input wire:model="image" type="file" id="image" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400">
        </div>
        @error('image') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max 2MB. Recommended: Square image, at least 256x256px.</p>
    </div>

    {{-- Screenshots Field --}}
    <div>
        <label for="screenshots" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Screenshots (up to 5)
        </label>
        @if (count($existingScreenshots) > 0)
            <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                @foreach ($existingScreenshots as $index => $screenshot)
                    <div class="relative group">
                        <img src="{{ Storage::disk('public')->url($screenshot) }}" alt="Screenshot {{ $index + 1 }}" class="h-32 w-full object-cover rounded-lg">
                        <button type="button" wire:click="removeExistingScreenshot({{ $index }})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        @if (count($existingScreenshots) < 5)
            <div class="mt-2">
                <input wire:model="screenshots" type="file" id="screenshots" accept="image/*" multiple class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400">
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Max 2MB each. You can add {{ 5 - count($existingScreenshots) }} more screenshot(s).
            </p>
        @endif
        @error('screenshots.*') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Platform Selection --}}
    <div class="space-y-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Available Platforms *
        </label>

        <div class="space-y-4">
            {{-- Mobile Toggle --}}
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input wire:model.live="hasMobile" type="checkbox" id="hasMobile" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div class="ml-3">
                    <label for="hasMobile" class="text-sm font-medium text-gray-700 dark:text-gray-300">Mobile App</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">iOS and/or Android</p>
                </div>
            </div>

            {{-- Mobile Links (shown when hasMobile is true) --}}
            @if ($hasMobile)
                <div class="ml-7 space-y-4 border-l-2 border-blue-200 dark:border-blue-800 pl-4">
                    <div>
                        <label for="appStoreUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            App Store URL
                        </label>
                        <input wire:model="appStoreUrl" type="url" id="appStoreUrl" placeholder="https://apps.apple.com/..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('appStoreUrl') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="playStoreUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Play Store URL
                        </label>
                        <input wire:model="playStoreUrl" type="url" id="playStoreUrl" placeholder="https://play.google.com/store/apps/..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('playStoreUrl') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif

            {{-- Desktop Toggle --}}
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input wire:model.live="hasDesktop" type="checkbox" id="hasDesktop" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700">
                </div>
                <div class="ml-3">
                    <label for="hasDesktop" class="text-sm font-medium text-gray-700 dark:text-gray-300">Desktop App</label>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Windows, macOS, and/or Linux</p>
                </div>
            </div>

            {{-- Desktop Links (shown when hasDesktop is true) --}}
            @if ($hasDesktop)
                <div class="ml-7 space-y-4 border-l-2 border-blue-200 dark:border-blue-800 pl-4">
                    <div>
                        <label for="windowsDownloadUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Windows Download URL
                        </label>
                        <input wire:model="windowsDownloadUrl" type="url" id="windowsDownloadUrl" placeholder="https://..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('windowsDownloadUrl') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="macosDownloadUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            macOS Download URL
                        </label>
                        <input wire:model="macosDownloadUrl" type="url" id="macosDownloadUrl" placeholder="https://..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('macosDownloadUrl') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="linuxDownloadUrl" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Linux Download URL
                        </label>
                        <input wire:model="linuxDownloadUrl" type="url" id="linuxDownloadUrl" placeholder="https://..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('linuxDownloadUrl') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif
        </div>

        @error('hasMobile') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Certification Checkbox --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input wire:model="certifiedNativephp" type="checkbox" id="certifiedNativephp" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div class="ml-3">
                <label for="certifiedNativephp" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    I certify this app is built with NativePHP *
                </label>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    By checking this box, you confirm that your application is built using NativePHP.
                    Submissions found not to be built with NativePHP may be rejected or removed from the showcase.
                </p>
            </div>
        </div>
        @error('certifiedNativephp') <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Form Actions --}}
    <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
        <div>
            @if ($isEditing)
                <button type="button" wire:click="delete" wire:confirm="Are you sure you want to delete this submission? This action cannot be undone." class="inline-flex items-center px-4 py-2 border border-red-300 dark:border-red-600 rounded-md shadow-sm text-sm font-medium text-red-700 dark:text-red-300 bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    Delete Submission
                </button>
            @endif
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('customer.showcase.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Cancel
            </a>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                <span wire:loading.remove>{{ $isEditing ? 'Update Submission' : 'Submit App' }}</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>
</form>
