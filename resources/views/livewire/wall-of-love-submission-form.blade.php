<form wire:submit="submit" class="space-y-6">
    {{-- Name Field --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Name *
        </label>
        <input wire:model="name" type="text" id="name" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Company Field --}}
    <div>
        <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Company (optional)
        </label>
        <input wire:model="company" type="text" id="company" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        @error('company') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Photo Field --}}
    <div>
        <label for="photo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Photo (optional)
        </label>
        <div class="mt-1">
            <input wire:model="photo" type="file" id="photo" accept="image/*" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/20 dark:file:text-blue-400">
        </div>
        @error('photo') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- URL Field --}}
    <div>
        <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Website or Social Media URL (optional)
        </label>
        <input wire:model="url" type="url" id="url" placeholder="https://example.com" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
        @error('url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Testimonial Field --}}
    <div>
        <label for="testimonial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Your story or testimonial (optional)
        </label>
        <textarea wire:model="testimonial" id="testimonial" rows="6" placeholder="Tell us about your experience with NativePHP..." class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
        @error('testimonial') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Share what you built, how NativePHP helped you, or what you love about the framework.</p>
    </div>

    <div class="flex justify-end space-x-3 pt-4">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancel
        </a>
        <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Submit Your Story
        </button>
    </div>
</form>