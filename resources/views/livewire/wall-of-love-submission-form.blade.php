<form wire:submit="submit" class="space-y-6">
    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(! $isEditing)
        {{-- Name Field --}}
        <flux:field>
            <flux:label>Name *</flux:label>
            <flux:input wire:model="name" />
            <flux:error name="name" />
        </flux:field>
    @endif

    {{-- Company Field --}}
    <flux:field>
        <flux:label>Company (optional)</flux:label>
        <flux:input wire:model="company" />
        <flux:error name="company" />
    </flux:field>

    {{-- Photo Field --}}
    <flux:field>
        <flux:label>Photo (optional)</flux:label>

        @if($isEditing && $existingPhoto)
            <div class="flex items-center gap-4">
                <img src="{{ Storage::disk('public')->url($existingPhoto) }}" alt="Current photo" class="size-16 rounded-full object-cover">
                <flux:button variant="danger" size="sm" wire:click="removeExistingPhoto" type="button">Remove photo</flux:button>
            </div>
        @endif

        <flux:input type="file" wire:model="photo" accept="image/*" />
        <flux:error name="photo" />
    </flux:field>

    @if(! $isEditing)
        {{-- URL Field --}}
        <flux:field>
            <flux:label>Website or Social Media URL (optional)</flux:label>
            <flux:input wire:model="url" type="url" placeholder="https://example.com" />
            <flux:error name="url" />
        </flux:field>

        {{-- Testimonial Field --}}
        <flux:field>
            <flux:label>Your story or testimonial (optional)</flux:label>
            <flux:textarea wire:model="testimonial" rows="6" placeholder="Tell us about your experience with NativePHP..." />
            <flux:error name="testimonial" />
            <flux:description>Share what you built, how NativePHP helped you, or what you love about the framework.</flux:description>
        </flux:field>
    @endif

    <div class="flex justify-end gap-3 pt-4">
        <flux:button variant="ghost" href="{{ route('dashboard') }}">Cancel</flux:button>
        <flux:button type="submit" variant="primary">{{ $isEditing ? 'Save Changes' : 'Submit Your Story' }}</flux:button>
    </div>
</form>
