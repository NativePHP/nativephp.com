<form wire:submit="submit" class="space-y-6">
    {{-- Warning for approved submissions being edited --}}
    @if ($isEditing && $showcase?->isApproved())
        <flux:callout variant="warning" icon="exclamation-triangle">
            <flux:callout.heading>Re-review Required</flux:callout.heading>
            <flux:callout.text>
                This submission is currently approved. If you make changes, it will need to be reviewed again before appearing in the showcase.
            </flux:callout.text>
        </flux:callout>
    @endif

    {{-- Title Field --}}
    <flux:field>
        <flux:label>App Name *</flux:label>
        <flux:input wire:model="title" />
        <flux:error name="title" />
    </flux:field>

    {{-- Description Field --}}
    <flux:field>
        <flux:label>Description *</flux:label>
        <flux:textarea wire:model="description" rows="4" placeholder="Tell us about your app and what it does..." />
        <flux:error name="description" />
        <flux:description>Maximum 2000 characters.</flux:description>
    </flux:field>

    {{-- Main Image Field --}}
    <flux:field>
        <flux:label>App Icon / Main Image</flux:label>
        @if ($existingImage)
            <div class="flex items-center gap-4">
                <img src="{{ Storage::disk('public')->url($existingImage) }}" alt="Current image" class="h-20 w-20 rounded-lg object-cover">
                <flux:button variant="danger" size="sm" type="button" wire:click="removeExistingImage">Remove</flux:button>
            </div>
        @endif
        <flux:input type="file" wire:model="image" accept="image/*" />
        <flux:error name="image" />
        <flux:description>Max 2MB. Recommended: Square image, at least 256x256px.</flux:description>
    </flux:field>

    {{-- Screenshots Field --}}
    <flux:field>
        <flux:label>Screenshots (up to 5)</flux:label>
        @if (count($existingScreenshots) > 0)
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
                @foreach ($existingScreenshots as $index => $screenshot)
                    <div wire:key="screenshot-{{ $index }}" class="group relative">
                        <img src="{{ Storage::disk('public')->url($screenshot) }}" alt="Screenshot {{ $index + 1 }}" class="h-32 w-full rounded-lg object-cover">
                        <button type="button" wire:click="removeExistingScreenshot({{ $index }})" class="absolute right-1 top-1 rounded-full bg-red-600 p-1 text-white opacity-0 transition-opacity group-hover:opacity-100">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        @if (count($existingScreenshots) < 5)
            <flux:input type="file" wire:model="screenshots" accept="image/*" multiple />
            <flux:description>Max 2MB each. You can add {{ 5 - count($existingScreenshots) }} more screenshot(s).</flux:description>
        @endif
        @error('screenshots.*') <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text> @enderror
    </flux:field>

    {{-- Platform Selection --}}
    <flux:fieldset>
        <flux:legend>Available Platforms *</flux:legend>

        <div class="space-y-4">
            {{-- Mobile Toggle --}}
            <flux:checkbox wire:model.live="hasMobile" label="Mobile App" description="iOS and/or Android" />

            {{-- Mobile Links (shown when hasMobile is true) --}}
            @if ($hasMobile)
                <div class="ml-7 space-y-4 border-l-2 border-blue-200 pl-4 dark:border-blue-800">
                    <flux:field>
                        <flux:label>App Store URL</flux:label>
                        <flux:input wire:model="appStoreUrl" type="url" placeholder="https://apps.apple.com/..." />
                        <flux:error name="appStoreUrl" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Play Store URL</flux:label>
                        <flux:input wire:model="playStoreUrl" type="url" placeholder="https://play.google.com/store/apps/..." />
                        <flux:error name="playStoreUrl" />
                    </flux:field>
                </div>
            @endif

            {{-- Desktop Toggle --}}
            <flux:checkbox wire:model.live="hasDesktop" label="Desktop App" description="Windows, macOS, and/or Linux" />

            {{-- Desktop Links (shown when hasDesktop is true) --}}
            @if ($hasDesktop)
                <div class="ml-7 space-y-4 border-l-2 border-blue-200 pl-4 dark:border-blue-800">
                    <flux:field>
                        <flux:label>Windows Download URL</flux:label>
                        <flux:input wire:model="windowsDownloadUrl" type="url" placeholder="https://..." />
                        <flux:error name="windowsDownloadUrl" />
                    </flux:field>

                    <flux:field>
                        <flux:label>macOS Download URL</flux:label>
                        <flux:input wire:model="macosDownloadUrl" type="url" placeholder="https://..." />
                        <flux:error name="macosDownloadUrl" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Linux Download URL</flux:label>
                        <flux:input wire:model="linuxDownloadUrl" type="url" placeholder="https://..." />
                        <flux:error name="linuxDownloadUrl" />
                    </flux:field>
                </div>
            @endif
        </div>

        <flux:error name="hasMobile" />
    </flux:fieldset>

    {{-- Certification Checkbox --}}
    <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
        <flux:checkbox wire:model="certifiedNativephp" label="I certify this app is built with NativePHP *" description="By checking this box, you confirm that your application is built using NativePHP. Submissions found not to be built with NativePHP may be rejected or removed from the showcase." />
        <flux:error name="certifiedNativephp" />
    </div>

    {{-- Form Actions --}}
    <div class="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-700">
        <div>
            @if ($isEditing)
                <flux:button variant="danger" type="button" wire:click="delete" wire:confirm="Are you sure you want to delete this submission? This action cannot be undone.">
                    Delete Submission
                </flux:button>
            @endif
        </div>
        <div class="flex gap-3">
            <flux:button variant="ghost" href="{{ route('customer.showcase.index') }}">Cancel</flux:button>
            <flux:button type="submit" variant="primary">
                {{ $isEditing ? 'Update Submission' : 'Submit App' }}
            </flux:button>
        </div>
    </div>
</form>
