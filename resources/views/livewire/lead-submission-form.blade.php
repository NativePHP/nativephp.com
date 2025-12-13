<div>
    @if ($submitted)
        <div class="p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Thank you for your enquiry!</h3>
            <p class="mt-2 text-gray-600 dark:text-gray-400">We've received your details and will be in touch soon.</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-6">
            @error('form')
                <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                </div>
            @enderror

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Name *
                </label>
                <input
                    wire:model="name"
                    type="text"
                    id="name"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Email Address *
                </label>
                <input
                    wire:model="email"
                    type="email"
                    id="email"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Company Name *
                </label>
                <input
                    wire:model="company"
                    type="text"
                    id="company"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                @error('company') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tell us about your app *
                </label>
                <textarea
                    wire:model="description"
                    id="description"
                    rows="5"
                    placeholder="Describe your app idea, the problems it solves, target platforms, and any specific requirements..."
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                ></textarea>
                @error('description') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="budget" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Budget Range *
                </label>
                <select
                    wire:model="budget"
                    id="budget"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                >
                    <option value="">Select a budget range</option>
                    @foreach ($budgets as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('budget') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                @if (config('services.turnstile.site_key'))
                    <div
                        wire:ignore
                        x-data="{
                            init() {
                                if (typeof turnstile !== 'undefined') {
                                    turnstile.render(this.$refs.turnstile, {
                                        sitekey: '{{ config('services.turnstile.site_key') }}',
                                        callback: (token) => {
                                            $wire.set('turnstileToken', token)
                                        },
                                        'expired-callback': () => {
                                            $wire.set('turnstileToken', '')
                                        }
                                    })
                                }
                            }
                        }"
                    >
                        <div x-ref="turnstile"></div>
                    </div>
                @endif
                @error('turnstileToken') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4">
                <button
                    type="submit"
                    class="w-full rounded-md bg-blue-600 px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>Submit Enquiry</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </div>
        </form>
    @endif
</div>
