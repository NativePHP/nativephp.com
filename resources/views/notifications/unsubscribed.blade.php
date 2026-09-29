<x-layouts.auth>
    <x-slot:title>Notifications Disabled</x-slot:title>

    <flux:card class="space-y-4">
        <div class="text-center">
            <flux:icon.check-circle variant="solid" class="mx-auto size-12 text-green-500" />
        </div>

        <flux:heading size="lg" class="text-center">Notifications Disabled</flux:heading>

        <flux:text class="text-center">
            New plugin notifications have been turned off for <strong>{{ $maskedEmail }}</strong>.
        </flux:text>

        <flux:text class="text-center text-sm">
            Did this happen by mistake?
        </flux:text>

        <div class="flex justify-center">
            <flux:button :href="$resubscribeUrl" variant="primary">
                Re-enable Notifications
            </flux:button>
        </div>
    </flux:card>
</x-layouts.auth>
