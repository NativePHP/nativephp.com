<div>
    <div class="mb-6">
        <a href="{{ route('customer.plugins.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
            <x-heroicon-s-arrow-left class="size-4" />
            <span class="font-medium">Plugins</span>
        </a>
        <flux:heading size="xl" class="mt-4">
            @if ($plugin->isDraft())
                Edit Draft Plugin
            @elseif ($plugin->isPending())
                Plugin Under Review
            @elseif ($plugin->isRejected())
                Plugin Rejected
            @elseif ($plugin->isApproved())
                Manage Plugin
            @endif
        </flux:heading>
        <div class="mt-1 flex items-center gap-3">
            <flux:text class="font-mono">{{ $plugin->display_name ?? $plugin->name }}</flux:text>
            <a href="{{ route('plugins.show', $plugin->routeParams()) }}" target="_blank" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                Preview Listing
                <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-3xl">
        {{-- Status-specific banners --}}
        @if ($plugin->isDraft())
            <flux:callout variant="info" icon="information-circle" class="mb-6">
                <flux:callout.heading>Draft Plugin</flux:callout.heading>
                <flux:callout.text>This plugin is a draft. Edit the details below, then submit for review when ready.</flux:callout.text>
            </flux:callout>
        @elseif ($plugin->isPending())
            <flux:callout variant="warning" icon="clock" class="mb-6">
                <flux:callout.heading>Under Review</flux:callout.heading>
                <flux:callout.text>Your plugin is currently being reviewed. You can withdraw it to make changes.</flux:callout.text>
                <x-slot name="actions">
                    <flux:modal.trigger name="withdraw-from-review">
                        <flux:button variant="ghost">Withdraw from Review</flux:button>
                    </flux:modal.trigger>
                </x-slot>
            </flux:callout>

            <flux:modal name="withdraw-from-review" class="md:w-96">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Withdraw from Review</flux:heading>
                        <flux:text class="mt-2">
                            Are you sure you want to withdraw this plugin from review? It will return to draft status.
                        </flux:text>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button variant="danger" wire:click="withdrawFromReview">Withdraw</flux:button>
                    </div>
                </div>
            </flux:modal>
        @elseif ($plugin->isRejected() && $plugin->rejection_reason)
            <flux:callout variant="danger" icon="x-circle" class="mb-6">
                <flux:callout.heading>Rejection Reason</flux:callout.heading>
                <flux:callout.text>{{ $plugin->rejection_reason }}</flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="danger" wire:click="returnToDraft">Return to Draft</flux:button>
                </x-slot>
            </flux:callout>
        @elseif ($plugin->isApproved())
            <flux:card class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">Listing Status</flux:heading>
                        <flux:text class="mt-1">
                            @if ($plugin->is_active)
                                Your plugin is publicly listed in the directory.
                            @else
                                Your plugin is de-listed and hidden from the directory.
                            @endif
                        </flux:text>
                    </div>
                    <flux:switch wire:click="toggleListing" :checked="$plugin->is_active" />
                </div>
            </flux:card>
        @endif

        {{-- Review Checks (show when review checks have been run) --}}
        @if ($plugin->review_checks)
            <flux:card class="mb-6">
                <flux:heading size="lg">Review Checks</flux:heading>
                <flux:text class="mt-1">Automated checks run against your repository.</flux:text>

                @php
                    $requiredChecks = [
                        ['key' => 'has_license_file', 'label' => 'License file (LICENSE or LICENSE.md)'],
                        ['key' => 'has_release_version', 'label' => 'Release version'],
                        ['key' => 'webhook_configured', 'label' => 'Webhook configured'],
                    ];
                    $optionalChecks = [
                        ['key' => 'supports_ios', 'label' => 'iOS support (resources/ios/)'],
                        ['key' => 'supports_android', 'label' => 'Android support (resources/android/)'],
                        ['key' => 'supports_js', 'label' => 'JavaScript support (resources/js/)'],
                        ['key' => 'requires_mobile_sdk', 'label' => 'Requires nativephp/mobile SDK'],
                    ];
                @endphp

                <flux:text class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Required for approval</flux:text>
                <ul class="mt-2 space-y-3">
                    @foreach ($requiredChecks as $check)
                        @php
                            $isPassing = $check['key'] === 'webhook_configured'
                                ? $plugin->webhook_installed
                                : ($plugin->review_checks[$check['key']] ?? false);
                        @endphp
                        <li>
                            <div class="flex items-center gap-2">
                                @if ($isPassing)
                                    <x-heroicon-s-check-circle class="size-5 shrink-0 text-green-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $check['label'] }}
                                        @if ($check['key'] === 'has_release_version' && ($plugin->review_checks['release_version'] ?? null))
                                            <code class="ml-1 rounded bg-gray-100 px-1 text-xs dark:bg-gray-700">{{ $plugin->review_checks['release_version'] }}</code>
                                        @endif
                                    </span>
                                @else
                                    <x-heroicon-s-x-circle class="size-5 shrink-0 text-red-400 dark:text-red-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $check['label'] }}</span>
                                @endif
                            </div>

                            @if ($check['key'] === 'webhook_configured')
                                @if (! $isPassing)
                                    <div class="ml-7 mt-2 rounded-md border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-900/20">
                                        <p class="text-sm text-amber-800 dark:text-amber-200">
                                            We couldn't automatically install the webhook. Please set it up manually:
                                        </p>
                                        <div class="mt-2">
                                            <label class="text-xs font-medium text-amber-900 dark:text-amber-100">Webhook URL</label>
                                            <div class="mt-1 flex items-center gap-2">
                                                <code class="block flex-1 overflow-x-auto rounded-md bg-white px-3 py-2 font-mono text-xs dark:bg-gray-800">{{ $plugin->getWebhookUrl() }}</code>
                                                <flux:button size="xs" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ $plugin->getWebhookUrl() }}')">
                                                    <x-heroicon-o-clipboard class="size-3" />
                                                </flux:button>
                                            </div>
                                        </div>
                                        <ol class="mt-3 list-inside list-decimal space-y-1 text-xs text-amber-700 dark:text-amber-300">
                                            <li>Go to your repository's <a href="{{ $plugin->repository_url }}/settings/hooks" target="_blank" rel="noopener noreferrer" class="font-medium underline hover:no-underline"><strong>Settings &rarr; Webhooks</strong></a></li>
                                            <li>Click <strong>Add webhook</strong></li>
                                            <li>Paste the URL above into the <strong>Payload URL</strong> field</li>
                                            <li>Set <strong>Content type</strong> to <code class="rounded bg-amber-100 px-1 dark:bg-amber-800">application/json</code></li>
                                            <li>Select events: <strong>Pushes</strong> and <strong>Releases</strong></li>
                                            <li>Click <strong>Add webhook</strong></li>
                                        </ol>

                                        <div class="mt-3 flex items-center gap-2">
                                            <flux:button size="sm" wire:click="retryWebhook">
                                                Retry automatic setup
                                            </flux:button>
                                            <flux:modal.trigger name="certify-webhook">
                                                <flux:button size="sm" variant="ghost">
                                                    I've installed it manually
                                                </flux:button>
                                            </flux:modal.trigger>
                                        </div>
                                    </div>
                                @elseif ($plugin->webhook_secret)
                                    <div class="ml-7 mt-1" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            <x-heroicon-o-chevron-right class="size-3 transition-transform" ::class="open && 'rotate-90'" />
                                            View setup instructions
                                        </button>
                                        <div x-show="open" x-cloak class="mt-2 rounded-md border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                            <div>
                                                <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Webhook URL</label>
                                                <div class="mt-1 flex items-center gap-2">
                                                    <code class="block flex-1 overflow-x-auto rounded-md bg-white px-3 py-2 font-mono text-xs dark:bg-gray-800">{{ $plugin->getWebhookUrl() }}</code>
                                                    <flux:button size="xs" variant="ghost" x-on:click="navigator.clipboard.writeText('{{ $plugin->getWebhookUrl() }}')">
                                                        <x-heroicon-o-clipboard class="size-3" />
                                                    </flux:button>
                                                </div>
                                            </div>
                                            <ol class="mt-3 list-inside list-decimal space-y-1 text-xs text-gray-600 dark:text-gray-400">
                                                <li>Go to your repository's <a href="{{ $plugin->repository_url }}/settings/hooks" target="_blank" rel="noopener noreferrer" class="font-medium underline hover:no-underline"><strong>Settings &rarr; Webhooks</strong></a></li>
                                                <li>Click <strong>Add webhook</strong></li>
                                                <li>Paste the URL above into the <strong>Payload URL</strong> field</li>
                                                <li>Set <strong>Content type</strong> to <code class="rounded bg-gray-200 px-1 dark:bg-gray-700">application/json</code></li>
                                                <li>Select events: <strong>Pushes</strong> and <strong>Releases</strong></li>
                                                <li>Click <strong>Add webhook</strong></li>
                                            </ol>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </li>
                    @endforeach
                </ul>

                <flux:text class="mt-5 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Additional checks</flux:text>
                <ul class="mt-2 space-y-3">
                    @foreach ($optionalChecks as $check)
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

                <flux:modal name="certify-webhook" class="md:w-96">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Confirm Webhook Installation</flux:heading>
                            <flux:text class="mt-2">
                                Please confirm that you have manually installed the webhook on your GitHub repository.
                            </flux:text>
                        </div>

                        <flux:callout variant="warning" icon="exclamation-triangle">
                            <flux:callout.text>
                                If the webhook is not correctly installed, we won't be able to sync your plugin's releases and metadata automatically.
                            </flux:callout.text>
                        </flux:callout>

                        <div class="flex gap-2">
                            <flux:spacer />
                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>
                            <flux:button variant="primary" wire:click="certifyWebhook">Confirm</flux:button>
                        </div>
                    </div>
                </flux:modal>
            </flux:card>
        @endif

        {{-- Editable fields for Draft plugins (with tabs) --}}
        @if ($plugin->isDraft())
            <flux:tab.group>
                <flux:tabs wire:model="activeTab">
                    <flux:tab name="details">Details</flux:tab>
                    <flux:tab name="submit">Submit for Review</flux:tab>
                </flux:tabs>

                <flux:tab.panel name="details">
                    {{-- GitHub Repo --}}
                    <a href="{{ $plugin->repository_url }}" target="_blank" rel="noopener noreferrer" class="block">
                        <flux:card class="mb-6 transition hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <x-icons.github class="size-5 text-gray-400 dark:text-gray-500" />
                                    <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</span>
                                </div>
                                <x-heroicon-o-arrow-top-right-on-square class="size-4 text-gray-400 dark:text-gray-500" />
                            </div>
                        </flux:card>
                    </a>

                    <form wire:submit="save" class="space-y-6">
                        {{-- Plugin Type --}}
                        @feature(App\Features\AllowPaidPlugins::class)
                        <flux:card>
                            <flux:heading size="lg">Type</flux:heading>
                            <flux:text class="mt-1">Is your plugin free or paid?</flux:text>

                            <div class="mt-6 space-y-4">
                                <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none"
                                    :class="$wire.pluginType === 'free' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                    <input type="radio" wire:model.live="pluginType" value="free" class="sr-only" />
                                    <span class="flex flex-1 flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Free Plugin</span>
                                        <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">Open source, hosted on Packagist</span>
                                    </span>
                                </label>

                                <label class="relative flex rounded-lg border p-4 transition focus:outline-none {{ $this->hasCompletedDeveloperOnboarding ? 'cursor-pointer' : 'cursor-not-allowed opacity-60' }}"
                                    :class="$wire.pluginType === 'paid' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 {{ $this->hasCompletedDeveloperOnboarding ? 'hover:bg-gray-50 dark:hover:bg-gray-700/50' : '' }}'">
                                    <input type="radio" wire:model.live="pluginType" value="paid" class="sr-only" {{ $this->hasCompletedDeveloperOnboarding ? '' : 'disabled' }} />
                                    <span class="flex flex-1 flex-col">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Paid Plugin</span>
                                        <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commercial plugin, hosted on plugins.nativephp.com</span>
                                    </span>
                                </label>
                                @if (! $this->hasCompletedDeveloperOnboarding)
                                    <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                                        To create paid plugins, you need to <a href="{{ route('customer.developer.onboarding') }}" class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400" wire:navigate>complete developer onboarding</a>.
                                    </flux:text>
                                @endif
                            </div>
                        </flux:card>

                        {{-- Pricing Tier (only when paid) --}}
                        @if ($pluginType === 'paid')
                            <flux:card :class="$errors->has('tier') ? '!border-red-500 dark:!border-red-400' : ''">
                                <flux:heading size="lg">Pricing Tier</flux:heading>
                                <flux:text class="mt-1">Choose a pricing tier for your plugin.</flux:text>

                                <div class="mt-6 space-y-4">
                                    @foreach (\App\Enums\PluginTier::cases() as $pluginTier)
                                        @php
                                            $regularPrice = $pluginTier->getPrices()[\App\Enums\PriceTier::Regular->value] / 100;
                                        @endphp
                                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none"
                                            :class="$wire.tier === '{{ $pluginTier->value }}' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                                            <input type="radio" wire:model.live="tier" value="{{ $pluginTier->value }}" class="sr-only" />
                                            <span class="flex flex-1 items-center justify-between">
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $pluginTier->label() }}</span>
                                                <span class="text-lg font-semibold text-gray-900 dark:text-white">${{ number_format($regularPrice) }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('tier')
                                    <flux:text class="mt-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror

                                <flux:text class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                    Actual sale price may vary due to discounts and offers. You keep 70% of the sale price. If a NativePHP Ultra subscriber purchases your plugin, you receive 100% of the sale price. Additional payment processing fees may apply.
                                </flux:text>
                            </flux:card>
                        @endif
                        @endfeature

                        {{-- Display Name --}}
                        <flux:card>
                            <flux:heading size="lg">Name <span class="text-sm font-normal text-gray-400 dark:text-gray-500">(optional)</span></flux:heading>
                            <flux:text class="mt-1">A display name for your plugin. If not set, your Composer package name will be used.</flux:text>

                            <div class="mt-4">
                                <flux:input
                                    wire:model="displayName"
                                    placeholder="{{ $plugin->name }}"
                                    maxlength="250"
                                />
                                <flux:text class="mt-2 text-xs">Maximum 250 characters</flux:text>
                            </div>
                        </flux:card>

                        {{-- Description --}}
                        <flux:card>
                            <flux:heading size="lg">Description</flux:heading>
                            <flux:text class="mt-1">Describe what your plugin does. This will be displayed in the plugin directory.</flux:text>

                            <div class="mt-4">
                                <flux:textarea
                                    wire:model="description"
                                    rows="5"
                                    placeholder="Describe what your plugin does, its key features, and how developers can use it..."
                                />
                                @error('description')
                                    <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror
                                <flux:text class="mt-2 text-xs">Maximum 1000 characters</flux:text>
                            </div>
                        </flux:card>

                        {{-- Icon --}}
                        <flux:card x-data="{ mode: @entangle('iconMode') }">
                            <flux:heading size="lg">Icon</flux:heading>
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
                                        <flux:button size="sm" variant="danger" icon="trash" wire:click="deleteIcon" type="button">Remove icon</flux:button>
                                    </div>
                                @endif

                                {{-- Gradient Icon Picker --}}
                                <div x-show="mode === 'gradient'" x-cloak>
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

                                        <flux:button wire:click="updateIcon" variant="filled" type="button">Save Icon</flux:button>
                                    </div>

                                    <flux:separator class="my-4" />
                                    <button type="button" @click="mode = 'upload'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                        Or upload your own logo instead
                                    </button>
                                </div>

                                {{-- Custom Logo Upload --}}
                                <div x-show="mode === 'upload'" x-cloak>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload a logo</label>
                                            <div class="mt-2 flex items-center gap-4">
                                                <input
                                                    type="file"
                                                    wire:model="logo"
                                                    accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                                    class="block text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/70"
                                                />
                                                <flux:button wire:click="uploadLogo" variant="filled" type="button">Upload</flux:button>
                                            </div>
                                            @error('logo')
                                                <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                            @enderror
                                            <flux:text class="mt-2 text-xs">PNG, JPG, SVG, or WebP. Max 1MB. Recommended: 256x256 pixels, square.</flux:text>
                                        </div>
                                    </div>

                                    <flux:separator class="my-4" />
                                    <button type="button" @click="mode = 'gradient'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                        Or choose a gradient icon instead
                                    </button>
                                </div>
                            </div>
                        </flux:card>

                        {{-- Support Channel --}}
                        <flux:card>
                            <flux:heading size="lg">Support</flux:heading>
                            <flux:text class="mt-1">How can users get support for your plugin? Provide an email address or a URL.</flux:text>

                            <div class="mt-4">
                                <flux:input
                                    wire:model="supportChannel"
                                    placeholder="support@example.com or https://..."
                                />
                                @error('supportChannel')
                                    <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                @enderror
                            </div>
                        </flux:card>

                        {{-- Save Button --}}
                        <div class="flex items-center justify-end">
                            <flux:button type="submit" variant="primary">Save Changes</flux:button>
                        </div>
                    </form>
                </flux:tab.panel>

                <flux:tab.panel name="submit">
                    <div class="space-y-6">
                        {{-- Plugin Summary --}}
                        <flux:card>
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4">
                                    @if ($plugin->hasLogo())
                                        <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-16 shrink-0 rounded-lg object-cover shadow-sm" />
                                    @elseif ($plugin->hasGradientIcon())
                                        <div class="grid size-16 shrink-0 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white shadow-sm">
                                            <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-8" />
                                        </div>
                                    @else
                                        <div class="grid size-16 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm">
                                            <x-vaadin-plug class="size-8" />
                                        </div>
                                    @endif
                                    <div>
                                        <flux:heading size="lg">{{ $plugin->display_name ?? $plugin->name }}</flux:heading>
                                        @if ($plugin->display_name)
                                            <flux:text class="font-mono text-xs">{{ $plugin->name }}</flux:text>
                                        @endif
                                        @if ($plugin->description)
                                            <flux:text class="mt-2">{{ $plugin->description }}</flux:text>
                                        @else
                                            <flux:text class="mt-2 text-gray-400 dark:text-gray-500">No description provided</flux:text>
                                        @endif
                                    </div>
                                </div>
                                @if ($plugin->isPaid() && $plugin->tier)
                                    <span class="inline-flex shrink-0 items-center text-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ $plugin->tier->label() }}
                                    </span>
                                @elseif ($plugin->isPaid())
                                    <span class="inline-flex shrink-0 items-center text-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        Paid
                                    </span>
                                @else
                                    <span class="inline-flex shrink-0 items-center text-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Free
                                    </span>
                                @endif
                            </div>

                            <flux:separator class="my-4" />

                            <dl class="grid grid-cols-2 gap-3">
                                {{-- Author --}}
                                <div class="col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Author</dt>
                                    <dd class="mt-1">
                                        <a href="{{ route('customer.developer.settings') }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            {{ $plugin->user->display_name }}
                                            <x-heroicon-o-pencil-square class="size-3" />
                                        </a>
                                    </dd>
                                </div>

                                {{-- Version --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Version</dt>
                                    <dd class="mt-1">
                                        @if ($plugin->latest_version)
                                            <a href="{{ $plugin->repository_url }}/releases" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                {{ $plugin->latest_version }}
                                                <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                            </a>
                                        @elseif ($plugin->review_checks['release_version'] ?? null)
                                            <a href="{{ $plugin->repository_url }}/releases" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                {{ $plugin->review_checks['release_version'] }}
                                                <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                                        @endif
                                    </dd>
                                </div>

                                {{-- License --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">License</dt>
                                    <dd class="mt-1">
                                        @if ($plugin->getLicense())
                                            <a href="{{ $plugin->getLicenseUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                {{ $plugin->getLicense() }}
                                                <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                                        @endif
                                    </dd>
                                </div>

                                {{-- iOS Version --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Min iOS</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $plugin->ios_version ?? ($plugin->review_checks['ios_min_version'] ?? '—') }}
                                    </dd>
                                </div>

                                {{-- Android Version --}}
                                <div>
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Min Android</dt>
                                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $plugin->android_version ?? ($plugin->review_checks['android_min_version'] ?? '—') }}
                                    </dd>
                                </div>

                                {{-- Support Channel --}}
                                <div class="col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Support Channel</dt>
                                    <dd class="mt-1">
                                        @if ($plugin->support_channel)
                                            @if (filter_var($plugin->support_channel, FILTER_VALIDATE_URL))
                                                <a href="{{ $plugin->support_channel }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ $plugin->support_channel }}
                                                    <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                                </a>
                                            @elseif (filter_var($plugin->support_channel, FILTER_VALIDATE_EMAIL))
                                                <a href="mailto:{{ $plugin->support_channel }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    {{ $plugin->support_channel }}
                                                    <x-heroicon-o-envelope class="size-3" />
                                                </a>
                                            @else
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->support_channel }}</span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400 dark:text-gray-500">Not set</span>
                                        @endif
                                    </dd>
                                </div>

                                {{-- Repository --}}
                                <div class="col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Repository</dt>
                                    <dd class="mt-1">
                                        <a href="{{ $plugin->repository_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            {{ $plugin->repository_url }}
                                            <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
                                        </a>
                                    </dd>
                                </div>
                            </dl>
                        </flux:card>

                        {{-- Notes --}}
                        <flux:card>
                            <flux:heading size="lg">Notes</flux:heading>
                            <flux:text class="mt-1">Share links to videos demonstrating the plugin in use or applications already available on the app stores using this plugin. Provide as much extra context for the review team so they can review your plugin more easily. <em>These notes will not be displayed on your plugin listing.</em></flux:text>

                            <div class="mt-4">
                                <flux:textarea
                                    wire:model="notes"
                                    rows="4"
                                    placeholder="Optional notes for the review team..."
                                />
                            </div>
                        </flux:card>

                        {{-- Preflight Checks & Submit --}}
                        <div class="flex items-center justify-end gap-3">
                            @if ($plugin->review_checks)
                                <flux:button variant="ghost" wire:click="runPreflightChecks">Re-run Checks</flux:button>
                            @endif
                            <flux:button variant="primary" wire:click="submitForReview">{{ $plugin->review_checks ? 'Submit for Review' : 'Submit' }}</flux:button>
                        </div>
                    </div>
                </flux:tab.panel>
            </flux:tab.group>
        @elseif ($plugin->isApproved())
            {{-- Editable fields for Approved plugins (no tabs) --}}

            {{-- GitHub Repo --}}
            <a href="{{ $plugin->repository_url }}" target="_blank" rel="noopener noreferrer" class="block">
                <flux:card class="mb-6 transition hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <x-icons.github class="size-5 text-gray-400 dark:text-gray-500" />
                            <span class="font-mono text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->name }}</span>
                        </div>
                        <x-heroicon-o-arrow-top-right-on-square class="size-4 text-gray-400 dark:text-gray-500" />
                    </div>
                </flux:card>
            </a>

            {{-- Read-only Type & Tier --}}
            <flux:card class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <flux:heading size="lg">Type</flux:heading>
                        <flux:text class="mt-1">
                            @if ($plugin->isPaid() && $plugin->tier)
                                Paid &mdash; {{ $plugin->tier->label() }}
                            @elseif ($plugin->isPaid())
                                Paid
                            @else
                                Free
                            @endif
                        </flux:text>
                    </div>
                </div>
            </flux:card>

            <form wire:submit="save" class="space-y-6">
                {{-- Display Name --}}
                <flux:card>
                    <flux:heading size="lg">Name <span class="text-sm font-normal text-gray-400 dark:text-gray-500">(optional)</span></flux:heading>
                    <flux:text class="mt-1">A display name for your plugin. If not set, your Composer package name will be used.</flux:text>

                    <div class="mt-4">
                        <flux:input
                            wire:model="displayName"
                            placeholder="{{ $plugin->name }}"
                            maxlength="250"
                        />
                        <flux:text class="mt-2 text-xs">Maximum 250 characters</flux:text>
                    </div>
                </flux:card>

                {{-- Description --}}
                <flux:card>
                    <flux:heading size="lg">Description</flux:heading>
                    <flux:text class="mt-1">Describe what your plugin does. This will be displayed in the plugin directory.</flux:text>

                    <div class="mt-4">
                        <flux:textarea
                            wire:model="description"
                            rows="5"
                            placeholder="Describe what your plugin does, its key features, and how developers can use it..."
                        />
                        @error('description')
                            <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                        <flux:text class="mt-2 text-xs">Maximum 1000 characters</flux:text>
                    </div>
                </flux:card>

                {{-- Icon --}}
                <flux:card x-data="{ mode: @entangle('iconMode') }">
                    <flux:heading size="lg">Icon</flux:heading>
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
                                <flux:button size="sm" variant="danger" icon="trash" wire:click="deleteIcon" type="button">Remove icon</flux:button>
                            </div>
                        @endif

                        {{-- Gradient Icon Picker --}}
                        <div x-show="mode === 'gradient'" x-cloak>
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

                                <flux:button wire:click="updateIcon" variant="filled" type="button">Save Icon</flux:button>
                            </div>

                            <flux:separator class="my-4" />
                            <button type="button" @click="mode = 'upload'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                Or upload your own logo instead
                            </button>
                        </div>

                        {{-- Custom Logo Upload --}}
                        <div x-show="mode === 'upload'" x-cloak>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload a logo</label>
                                    <div class="mt-2 flex items-center gap-4">
                                        <input
                                            type="file"
                                            wire:model="logo"
                                            accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                            class="block text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-indigo-900/50 dark:file:text-indigo-300 dark:hover:file:bg-indigo-900/70"
                                        />
                                        <flux:button wire:click="uploadLogo" variant="filled" type="button">Upload</flux:button>
                                    </div>
                                    @error('logo')
                                        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                                    @enderror
                                    <flux:text class="mt-2 text-xs">PNG, JPG, SVG, or WebP. Max 1MB. Recommended: 256x256 pixels, square.</flux:text>
                                </div>
                            </div>

                            <flux:separator class="my-4" />
                            <button type="button" @click="mode = 'gradient'" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                Or choose a gradient icon instead
                            </button>
                        </div>
                    </div>
                </flux:card>

                {{-- Support Channel --}}
                <flux:card>
                    <flux:heading size="lg">Support</flux:heading>
                    <flux:text class="mt-1">How can users get support for your plugin? Provide an email address or a URL.</flux:text>

                    <div class="mt-4">
                        <flux:input
                            wire:model="supportChannel"
                            placeholder="support@example.com or https://..."
                        />
                        @error('supportChannel')
                            <flux:text class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </flux:card>

                {{-- Save Button --}}
                <div class="flex items-center justify-end">
                    <flux:button type="submit" variant="primary">Save Changes</flux:button>
                </div>
            </form>
        @else
            {{-- Read-only display for Pending/Rejected --}}
            <flux:card class="mb-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-4">
                        @if ($plugin->hasLogo())
                            <img src="{{ $plugin->getLogoUrl() }}" alt="{{ $plugin->name }} logo" class="size-16 shrink-0 rounded-lg object-cover shadow-sm" />
                        @elseif ($plugin->hasGradientIcon())
                            <div class="grid size-16 shrink-0 place-items-center rounded-lg bg-gradient-to-br {{ $plugin->getGradientClasses() }} text-white shadow-sm">
                                <x-dynamic-component :component="'heroicon-o-' . $plugin->icon_name" class="size-8" />
                            </div>
                        @else
                            <div class="grid size-16 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm">
                                <x-vaadin-plug class="size-8" />
                            </div>
                        @endif
                        <div>
                            <flux:heading size="lg">{{ $plugin->display_name ?? $plugin->name }}</flux:heading>
                            @if ($plugin->description)
                                <flux:text class="mt-2">{{ $plugin->description }}</flux:text>
                            @else
                                <flux:text class="mt-2 text-gray-400 dark:text-gray-500">No description provided</flux:text>
                            @endif
                        </div>
                    </div>
                    @if ($plugin->isPaid() && $plugin->tier)
                        <span class="inline-flex shrink-0 items-center text-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                            {{ $plugin->tier->label() }}
                        </span>
                    @elseif ($plugin->isPaid())
                        <span class="inline-flex shrink-0 items-center text-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                            Paid
                        </span>
                    @else
                        <span class="inline-flex shrink-0 items-center text-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            Free
                        </span>
                    @endif
                </div>

                <flux:separator class="my-4" />

                <dl class="grid grid-cols-2 gap-3">
                    {{-- Author --}}
                    <div class="col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Author</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $plugin->user->display_name }}
                        </dd>
                    </div>

                    {{-- Version --}}
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Version</dt>
                        <dd class="mt-1">
                            @if ($plugin->latest_version)
                                <a href="{{ $plugin->repository_url }}/releases" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $plugin->latest_version }}
                                    <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                </a>
                            @elseif ($plugin->review_checks['release_version'] ?? null)
                                <a href="{{ $plugin->repository_url }}/releases" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $plugin->review_checks['release_version'] }}
                                    <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                </a>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                            @endif
                        </dd>
                    </div>

                    {{-- License --}}
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">License</dt>
                        <dd class="mt-1">
                            @if ($plugin->getLicense())
                                <a href="{{ $plugin->getLicenseUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ $plugin->getLicense() }}
                                    <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                </a>
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-500">&mdash;</span>
                            @endif
                        </dd>
                    </div>

                    {{-- iOS Version --}}
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Min iOS</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $plugin->ios_version ?? ($plugin->review_checks['ios_min_version'] ?? '—') }}
                        </dd>
                    </div>

                    {{-- Android Version --}}
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Min Android</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-white">
                            {{ $plugin->android_version ?? ($plugin->review_checks['android_min_version'] ?? '—') }}
                        </dd>
                    </div>

                    {{-- Support Channel --}}
                    <div class="col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Support Channel</dt>
                        <dd class="mt-1">
                            @if ($plugin->support_channel)
                                @if (filter_var($plugin->support_channel, FILTER_VALIDATE_URL))
                                    <a href="{{ $plugin->support_channel }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $plugin->support_channel }}
                                        <x-heroicon-o-arrow-top-right-on-square class="size-3" />
                                    </a>
                                @elseif (filter_var($plugin->support_channel, FILTER_VALIDATE_EMAIL))
                                    <a href="mailto:{{ $plugin->support_channel }}" class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        {{ $plugin->support_channel }}
                                        <x-heroicon-o-envelope class="size-3" />
                                    </a>
                                @else
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $plugin->support_channel }}</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-400 dark:text-gray-500">Not set</span>
                            @endif
                        </dd>
                    </div>

                    {{-- Repository --}}
                    <div class="col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Repository</dt>
                        <dd class="mt-1">
                            <a href="{{ $plugin->repository_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">
                                {{ $plugin->repository_url }}
                                <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
                            </a>
                        </dd>
                    </div>
                </dl>
            </flux:card>

            @if ($plugin->notes)
                <flux:card class="mb-6">
                    <flux:heading size="lg">Submission Notes</flux:heading>
                    <flux:text class="mt-2">{{ $plugin->notes }}</flux:text>
                </flux:card>
            @endif
        @endif

    </div>

    @script
    <script>
        $wire.on('scroll-to-first-error', () => {
            setTimeout(() => {
                const firstError = document.querySelector('.text-red-600, .text-red-500, [data-flux-error]:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }, 100);
        });
    </script>
    @endscript
</div>
