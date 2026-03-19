<div>
    <div class="mb-6">
        <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
            <x-heroicon-s-arrow-left class="size-4" />
            <span class="font-medium">Plugins</span>
        </a>
        <flux:heading size="xl" class="mt-4">Edit Plugin</flux:heading>
        <flux:text class="font-mono">{{ $plugin->name }}</flux:text>
    </div>

    <div class="mx-auto max-w-3xl">
        {{-- Success/Error Messages --}}
        @if (session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        @if (session('error'))
            <flux:callout variant="danger" icon="x-circle" class="mb-6">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        {{-- Webhook Status --}}
        @if ($plugin->webhook_secret)
            @if ($plugin->webhook_installed)
                <flux:callout variant="success" icon="check-circle" class="mb-6">
                    <flux:callout.heading>Webhook Configured</flux:callout.heading>
                    <flux:callout.text>
                        The GitHub webhook has been automatically configured for your repository. Your plugin data will sync automatically when you push changes or create releases.
                    </flux:callout.text>
                </flux:callout>
            @else
                <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
                    <flux:callout.heading>Manual Webhook Setup Required</flux:callout.heading>
                    <flux:callout.text>
                        We couldn't automatically install the webhook on your repository. Please set it up manually to enable automatic syncing.
                    </flux:callout.text>
                </flux:callout>

                <flux:card class="mb-6">
                    <div>
                        <flux:heading>Webhook URL</flux:heading>
                        <div class="mt-2 flex items-center gap-2">
                            <code class="block flex-1 overflow-x-auto rounded-md bg-gray-100 px-3 py-2 font-mono text-sm dark:bg-gray-700">{{ $plugin->getWebhookUrl() }}</code>
                            <flux:button size="sm" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ $plugin->getWebhookUrl() }}')">
                                <x-heroicon-o-clipboard class="size-4" />
                            </flux:button>
                        </div>
                    </div>

                    <div class="mt-4 rounded-md bg-gray-50 p-4 dark:bg-gray-700/50">
                        <flux:heading>Setup Instructions</flux:heading>
                        <ol class="mt-2 list-inside list-decimal space-y-1 text-sm text-gray-600 dark:text-gray-400">
                            <li>Go to your repository's <strong>Settings &rarr; Webhooks</strong></li>
                            <li>Click <strong>Add webhook</strong></li>
                            <li>Paste the Webhook URL above into the <strong>Payload URL</strong> field</li>
                            <li>Set <strong>Content type</strong> to <code class="rounded bg-gray-100 px-1 dark:bg-gray-600">application/json</code></li>
                            <li>Under events, select <strong>Pushes</strong> and <strong>Releases</strong></li>
                            <li>Click <strong>Add webhook</strong></li>
                        </ol>
                    </div>
                </flux:card>
            @endif
        @endif

        {{-- Plugin Status --}}
        <flux:card class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if ($plugin->hasLogo())
                        <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-10 rounded-lg object-cover" />
                    @elseif ($plugin->hasGradientIcon())
                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white">
                            <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-5" />
                        </div>
                    @else
                        <div class="grid size-10 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white">
                            <x-vaadin-plug class="size-5" />
                        </div>
                    @endif
                    <div>
                        <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</span>
                        <flux:text class="text-xs">
                            {{ $plugin->type->label() }} plugin
                            @if ($plugin->latest_version)
                                <span class="text-gray-400 dark:text-gray-500">&bull;</span>
                                v{{ $plugin->latest_version }}
                            @endif
                        </flux:text>
                    </div>
                </div>
                <x-customer.status-badge :status="$plugin->isPending() ? 'Pending Review' : ($plugin->isApproved() ? 'Approved' : 'Rejected')" />
            </div>
        </flux:card>

        {{-- Review Checks --}}
        @if ($plugin->review_checks)
            <flux:card class="mb-6">
                <flux:heading size="lg">Review Checks</flux:heading>
                <flux:text class="mt-1">Automated checks run against your repository.</flux:text>

                @php
                    $checks = [
                        ['key' => 'supports_ios', 'label' => 'iOS support (resources/ios/)'],
                        ['key' => 'supports_android', 'label' => 'Android support (resources/android/)'],
                        ['key' => 'supports_js', 'label' => 'JavaScript support (resources/js/)'],
                        ['key' => 'has_support_email', 'label' => 'Support email in README'],
                        ['key' => 'requires_mobile_sdk', 'label' => 'Requires nativephp/mobile SDK'],
                    ];
                @endphp

                <ul class="mt-4 space-y-3">
                    @foreach ($checks as $check)
                        <li class="flex items-center gap-2">
                            @if ($plugin->review_checks[$check['key']] ?? false)
                                <x-heroicon-s-check-circle class="size-5 shrink-0 text-green-500" />
                            @else
                                <x-heroicon-s-x-circle class="size-5 shrink-0 text-gray-300 dark:text-gray-600" />
                            @endif
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $check['label'] }}</span>
                        </li>
                    @endforeach
                </ul>

                @if ($plugin->review_checks['support_email'] ?? null)
                    <flux:text class="mt-3 text-xs">
                        Support email: {{ $plugin->review_checks['support_email'] }}
                    </flux:text>
                @endif

                @if ($plugin->review_checks['mobile_sdk_constraint'] ?? null)
                    <flux:text class="mt-1 text-xs">
                        SDK constraint: <code class="rounded bg-gray-100 px-1 dark:bg-gray-700">{{ $plugin->review_checks['mobile_sdk_constraint'] }}</code>
                    </flux:text>
                @endif

                @if ($plugin->reviewed_at)
                    <flux:text class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                        Last checked {{ $plugin->reviewed_at->diffForHumans() }}
                    </flux:text>
                @endif
            </flux:card>
        @endif

        {{-- Plugin Icon --}}
        <flux:card class="mb-6" x-data="{ mode: @entangle('iconMode') }">
            <flux:heading size="lg">Plugin Icon</flux:heading>
            <flux:text class="mt-1">Choose a gradient and icon, or upload your own logo.</flux:text>

            <div class="mt-4">
                {{-- Current Icon Preview --}}
                @if ($plugin->hasCustomIcon())
                    <div class="mb-4 flex items-center gap-4">
                        @if ($plugin->hasLogo())
                            <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-16 rounded-lg object-cover shadow-sm" />
                        @elseif ($plugin->hasGradientIcon())
                            <div class="grid size-16 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white shadow-sm">
                                <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-8" />
                            </div>
                        @endif
                        <flux:button size="sm" variant="danger" wire:click="deleteIcon">
                            <x-heroicon-o-trash class="size-4 mr-1" />
                            Remove icon
                        </flux:button>
                    </div>
                @endif

                {{-- Gradient Icon Picker --}}
                <div x-show="mode === 'gradient'" x-cloak>
                    <form wire:submit="updateIcon">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose a gradient</label>
                                <div class="mt-2 grid grid-cols-4 gap-3 sm:grid-cols-8">
                                    @foreach (\App\Models\Plugin::gradientPresets() as $key => $classes)
                                        <label class="relative cursor-pointer">
                                            <input
                                                type="radio"
                                                wire:model="iconGradient"
                                                value="{{ $key }}"
                                                class="peer sr-only"
                                            />
                                            <div class="size-12 rounded-lg bg-gradient-to-br {{ $classes }} ring-2 ring-transparent ring-offset-2 transition-all peer-checked:ring-indigo-500 peer-focus:ring-indigo-500 hover:scale-105 dark:ring-offset-gray-800"></div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('iconGradient')
                                    <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror
                            </div>

                            <flux:input
                                wire:model="iconName"
                                label="Heroicon name"
                                placeholder="cube"
                                description="Enter a Heroicon outline name, e.g., cube, sparkles, bolt."
                            />
                            @error('iconName')
                                <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror

                            <flux:button type="submit" variant="primary">Save Icon</flux:button>
                        </div>
                    </form>

                    <flux:separator class="my-4" />
                    <button type="button" @click="mode = 'upload'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                        Or upload your own logo instead
                    </button>
                </div>

                {{-- Custom Logo Upload --}}
                <div x-show="mode === 'upload'" x-cloak>
                    <form wire:submit="uploadLogo" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload a logo</label>
                            <div class="mt-2 flex items-center gap-4">
                                <input
                                    type="file"
                                    wire:model="logo"
                                    accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                    class="block text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/70"
                                />
                                <flux:button type="submit" variant="primary">Upload</flux:button>
                            </div>
                            @error('logo')
                                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                            <flux:text class="mt-2 text-xs">PNG, JPG, SVG, or WebP. Max 1MB. Recommended: 256x256 pixels, square.</flux:text>
                        </div>
                    </form>

                    <flux:separator class="my-4" />
                    <button type="button" @click="mode = 'gradient'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                        Or choose a gradient icon instead
                    </button>
                </div>
            </div>
        </flux:card>

        {{-- Description Form --}}
        <flux:card class="mb-6">
            <flux:heading size="lg">Plugin Description</flux:heading>
            <flux:text class="mt-1">Describe what your plugin does. This will be displayed in the plugin directory.</flux:text>

            <form wire:submit="updateDescription" class="mt-4">
                <flux:textarea
                    wire:model="description"
                    rows="5"
                    placeholder="Describe what your plugin does, its key features, and how developers can use it..."
                />
                @error('description')
                    <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                @enderror
                <flux:text class="mt-2 text-xs">Maximum 1000 characters</flux:text>

                <div class="mt-4 flex justify-end">
                    <flux:button type="submit" variant="primary">Save Description</flux:button>
                </div>
            </form>
        </flux:card>

        {{-- Rejection Reason --}}
        @if ($plugin->isRejected() && $plugin->rejection_reason)
            <flux:callout variant="danger" icon="x-circle">
                <flux:callout.heading>Rejection Reason</flux:callout.heading>
                <flux:callout.text>{{ $plugin->rejection_reason }}</flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="danger" wire:click="resubmit">Resubmit for Review</flux:button>
                </x-slot>
            </flux:callout>
        @endif
    </div>
</div>
