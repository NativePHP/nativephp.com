<div>
    <div class="mb-6">
        <flux:heading size="xl">Integrations</flux:heading>
        <flux:text>Connect your accounts to unlock additional features</flux:text>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session()->has('warning'))
        <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
            <flux:callout.text>{{ session('warning') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session()->has('error'))
        <flux:callout variant="danger" icon="x-circle" class="mb-6">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    {{-- Info Section --}}
    <flux:card class="mb-6">
        <flux:heading>About Integrations</flux:heading>
        <div class="mt-4 prose dark:prose-invert prose-sm max-w-none">
            <ul class="list-disc list-inside space-y-2">
                <li><strong>GitHub:</strong> Max license holders can access the private <code>nativephp/mobile</code> repository. Plugin Dev Kit license holders and Ultra subscribers can access <code>nativephp/claude-code</code>.</li>
                <li><strong>Discord:</strong> Max license holders receive a special "Max" role in the NativePHP Discord server.</li>
            </ul>
            <p class="mt-4">
                Need help? Join our <a href="https://discord.gg/nativephp" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">Discord community</a>.
            </p>
        </div>
    </flux:card>

    {{-- Claude Plugins Access --}}
    <div class="mb-6">
        <livewire:claude-plugins-access-banner :inline="true" />
    </div>

    @if(auth()->user()->hasMaxAccess())
        <div class="space-y-6">
            <livewire:git-hub-access-banner :inline="true" />
            <livewire:discord-access-banner :inline="true" />
        </div>
    @endif
</div>
