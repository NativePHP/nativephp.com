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
                <li><strong>Discord:</strong> Max license holders and Ultra subscribers receive a special "Ultra" role in the NativePHP Discord server. Early Access Program customers receive the "Early Adopter" role. Masterclass students receive the "Master" role, unlocking private channels.</li>
            </ul>
            <p class="mt-4">
                Need help? Join our <a href="https://discord.gg/nativephp" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">Discord community</a>.
            </p>
        </div>
    </flux:card>

    {{-- GitHub Account --}}
    <flux:card class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <svg class="h-6 w-6 flex-shrink-0 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                </svg>
                <div>
                    <flux:heading>GitHub Account</flux:heading>
                    @if (auth()->user()->github_username)
                        <flux:text class="mt-1">Connected as <span class="font-mono font-medium text-gray-900 dark:text-white">{{ '@' . auth()->user()->github_username }}</span></flux:text>
                    @else
                        <flux:text class="mt-1">Connect your GitHub account to access private repositories and publish plugins.</flux:text>
                    @endif
                </div>
            </div>
            <div class="lg:flex-shrink-0">
                @if (auth()->user()->github_username)
                    <flux:modal.trigger name="disconnect-github">
                        <flux:button variant="danger">Disconnect</flux:button>
                    </flux:modal.trigger>
                @else
                    <flux:button href="{{ route('github.redirect', ['return' => route('customer.integrations')]) }}">Connect GitHub</flux:button>
                @endif
            </div>
        </div>
    </flux:card>

    {{-- Disconnect GitHub Confirmation Modal --}}
    @if (auth()->user()->github_username)
        <flux:modal name="disconnect-github" class="md:w-[32rem]">
            <form action="{{ route('github.disconnect') }}" method="POST">
                @csrf
                @method('DELETE')

                <flux:heading size="lg">Disconnect GitHub?</flux:heading>

                <flux:text class="mt-2">
                    This will unlink <span class="font-mono font-medium">{{ '@' . auth()->user()->github_username }}</span> from your NativePHP account.
                </flux:text>

                <flux:callout variant="warning" icon="exclamation-triangle" class="mt-4">
                    <flux:callout.text>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Any access to the private <code>nativephp/mobile</code> and <code>nativephp/claude-code</code> repositories will be revoked.</li>
                            <li>Your GitHub repositories will no longer be available when submitting or managing plugins.</li>
                            <li>You can reconnect at any time. When re-authorizing, be sure to grant access to any organizations whose repositories you need.</li>
                        </ul>
                    </flux:callout.text>
                </flux:callout>

                @if (! auth()->user()->password)
                    <flux:callout variant="danger" icon="exclamation-triangle" class="mt-4">
                        <flux:callout.text>
                            Your account uses GitHub to sign in and has no password set. Consider setting a password in
                            <a href="{{ route('customer.settings') }}" class="underline">Settings</a> before disconnecting.
                        </flux:callout.text>
                    </flux:callout>
                @endif

                <div class="mt-6 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="danger">Disconnect GitHub</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    {{-- Claude Plugins Access --}}
    <div class="mb-6">
        <livewire:claude-plugins-access-banner :inline="true" />
    </div>

    <div class="space-y-6">
        @if(auth()->user()->hasMobileRepoAccess())
            <livewire:git-hub-access-banner :inline="true" />
        @endif

        @if(auth()->user()->hasMaxAccess() || auth()->user()->hasUltraAccess() || auth()->user()->isEapCustomer() || auth()->user()->hasPurchasedMasterclass())
            <livewire:discord-access-banner :inline="true" />
        @endif
    </div>
</div>
