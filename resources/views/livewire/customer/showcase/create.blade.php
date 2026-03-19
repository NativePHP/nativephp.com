<div class="max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">Submit Your App to the Showcase</flux:heading>
        <flux:text class="mt-2">Share your NativePHP app with the community! Your submission will be reviewed by our team.</flux:text>
    </div>

    <flux:card>
        <flux:callout color="blue" icon="information-circle" class="mb-6">
            <flux:callout.heading>Showcase Guidelines</flux:callout.heading>
            <flux:callout.text>
                <ul class="mt-1 list-disc list-inside space-y-1">
                    <li>Your app must be built with NativePHP</li>
                    <li>Include clear screenshots showcasing your app</li>
                    <li>Provide download links or store URLs where users can get your app</li>
                    <li>Submissions are reviewed before being published</li>
                </ul>
            </flux:callout.text>
        </flux:callout>

        <livewire:showcase-submission-form />
    </flux:card>
</div>
