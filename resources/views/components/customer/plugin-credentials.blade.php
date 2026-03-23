@props(['pluginLicenseKey'])

<flux:card class="mb-6">
    <div class="flex items-start justify-between">
        <div>
            <flux:heading>Your Plugin Credentials</flux:heading>
            <flux:text class="mt-1">
                Use these credentials with Composer to install plugins from the NativePHP Plugin Marketplace.
            </flux:text>
        </div>
        <flux:modal.trigger name="rotate-plugin-key">
            <flux:button size="sm" icon="arrow-path" tooltip="Rotate key" />
        </flux:modal.trigger>
    </div>

    <details class="mt-3">
        <summary class="cursor-pointer text-xs font-medium text-indigo-700 hover:text-indigo-900 dark:text-indigo-300 dark:hover:text-indigo-100">
            How to configure Composer
        </summary>
        <div class="mt-2 space-y-2">
            <flux:text class="text-xs">1. Add the NativePHP plugins repository:</flux:text>
            <div class="group flex items-center rounded bg-zinc-900">
                <div class="min-w-0 flex-1 overflow-x-auto p-3">
                    <code class="block whitespace-pre pr-4 font-mono text-xs text-zinc-100">composer config repositories.nativephp-plugins composer https://plugins.nativephp.com</code>
                </div>
                <button
                    type="button"
                    x-data x-on:click="navigator.clipboard.writeText('composer config repositories.nativephp-plugins composer https://plugins.nativephp.com')"
                    class="shrink-0 self-stretch bg-zinc-900 px-3 text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200"
                    title="Copy command"
                >
                    <flux:icon name="clipboard" variant="outline" class="size-4" />
                </button>
            </div>
            <flux:text class="text-xs">2. Configure your credentials:</flux:text>
            <div class="group flex items-center rounded bg-zinc-900">
                <div class="min-w-0 flex-1 overflow-x-auto p-3">
                    <code class="block whitespace-pre pr-4 font-mono text-xs text-zinc-100">composer config http-basic.plugins.nativephp.com {{ auth()->user()->email }} {{ $pluginLicenseKey }}</code>
                </div>
                <button
                    type="button"
                    x-data x-on:click="navigator.clipboard.writeText('composer config http-basic.plugins.nativephp.com {{ auth()->user()->email }} {{ $pluginLicenseKey }}')"
                    class="shrink-0 self-stretch bg-zinc-900 px-3 text-zinc-400 hover:bg-zinc-800 hover:text-zinc-200"
                    title="Copy command"
                >
                    <flux:icon name="clipboard" variant="outline" class="size-4" />
                </button>
            </div>
        </div>
    </details>
</flux:card>

{{-- Rotate Key Confirmation Modal --}}
<flux:modal name="rotate-plugin-key" class="md:w-96">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Rotate Plugin License Key</flux:heading>
            <flux:text class="mt-2">
                Are you sure you want to rotate your plugin license key? This action cannot be undone.
            </flux:text>
        </div>

        <flux:callout variant="warning" icon="exclamation-triangle">
            <flux:callout.heading>After rotating your key, you will need to:</flux:callout.heading>
            <flux:callout.text>
                <ul class="mt-1 list-disc pl-5 text-sm">
                    <li>Update your <code class="font-mono">auth.json</code> file in all projects</li>
                    <li>Reconfigure Composer credentials on any CI/CD systems</li>
                    <li>Update any deployment scripts that reference the old key</li>
                </ul>
            </flux:callout.text>
        </flux:callout>

        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="rotateKey">Rotate Key</flux:button>
        </div>
    </div>
</flux:modal>
