<div class="max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">Join our Wall of Love!</flux:heading>
        <flux:text class="mt-2">As an early adopter, your story matters. Share your experience with NativePHP and inspire other developers in the community.</flux:text>
    </div>

    <flux:card>
        @if(session()->has('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        <flux:callout color="blue" icon="information-circle" class="mb-6">
            <flux:callout.heading>You're an Early Adopter!</flux:callout.heading>
            <flux:callout.text>
                <p>Thank you for supporting NativePHP from the beginning. As a reward, you can appear permanently on our <a href="{{ route('wall-of-love') }}" target="_blank" class="underline">Wall of Love</a>.</p>
                <p class="mt-1">Your submission will be reviewed by our team and, once approved, will appear on the page.</p>
            </flux:callout.text>
        </flux:callout>

        <livewire:wall-of-love-submission-form />
    </flux:card>
</div>
