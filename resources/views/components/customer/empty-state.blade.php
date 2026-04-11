@props(['icon', 'title', 'description'])

<div class="text-center">
    <flux:card class="p-6">
        <flux:icon :name="$icon" variant="outline" class="mx-auto size-12 text-zinc-400" />
        <flux:heading class="mt-2">{{ $title }}</flux:heading>
        <flux:text class="mt-1">{{ $description }}</flux:text>
        @if($slot->isNotEmpty())
            <div class="mt-6">
                {{ $slot }}
            </div>
        @endif
    </flux:card>
</div>
