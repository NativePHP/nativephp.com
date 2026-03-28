<div>
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2">
                <li>
                    <a href="{{ route('customer.plugins.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        Plugins
                    </a>
                </li>
                <li>
                    <x-heroicon-s-chevron-right class="size-4 text-gray-400" />
                </li>
                <li>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Submit Plugin</span>
                </li>
            </ol>
        </nav>
        <flux:heading size="xl" class="mt-2">Submit Your Plugin</flux:heading>
        <flux:text>Add your plugin to the NativePHP Plugin Marketplace</flux:text>
    </div>

    <div class="mx-auto max-w-3xl space-y-8">
        {{-- Session Error --}}
        @if (session('error'))
            <flux:callout variant="danger" icon="x-circle">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <flux:callout variant="danger" icon="x-circle">
                <flux:callout.heading>Please fix the following errors:</flux:callout.heading>
                <flux:callout.text>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </flux:callout.text>
            </flux:callout>
        @endif

        {{-- GitHub Connection Required --}}
        @if (!auth()->user()->github_id)
            <flux:callout variant="warning" icon="exclamation-triangle">
                <flux:callout.heading>GitHub Connection Required</flux:callout.heading>
                <flux:callout.text>
                    To submit a plugin, you need to connect your GitHub account so we can access your repository and automatically set up webhooks.
                </flux:callout.text>
                <x-slot name="actions">
                    <flux:button variant="filled" href="{{ route('github.redirect', ['return' => route('customer.plugins.create')]) }}">
                        <x-heroicon-o-link class="size-4 mr-1" />
                        Connect GitHub Account
                    </flux:button>
                </x-slot>
            </flux:callout>
        @else
            <form wire:submit="submitPlugin">
                {{-- Plugin Type --}}
                @feature(App\Features\AllowPaidPlugins::class)
                <flux:card>
                    <flux:heading size="lg">Plugin Type</flux:heading>
                    <flux:text class="mt-1">Is your plugin free or paid?</flux:text>

                    <div class="mt-6 space-y-4">
                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none"
                            :class="$wire.pluginType === 'free' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <input type="radio" wire:model="pluginType" value="free" class="sr-only" />
                            <span class="flex flex-1 flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Free Plugin</span>
                                <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">Open source, hosted on Packagist/GitHub</span>
                            </span>
                        </label>

                        <label class="relative flex cursor-pointer rounded-lg border p-4 transition focus:outline-none"
                            :class="$wire.pluginType === 'paid' ? 'border-indigo-500 bg-indigo-50 dark:border-indigo-400 dark:bg-indigo-950/30' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50'">
                            <input type="radio" wire:model="pluginType" value="paid" class="sr-only" />
                            <span class="flex flex-1 flex-col">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">Paid Plugin</span>
                                <span class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commercial plugin, hosted on plugins.nativephp.com</span>
                            </span>
                        </label>
                    </div>

                    @error('pluginType')
                        <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror
                </flux:card>
                @else
                <input type="hidden" wire:model="pluginType" value="free" />
                @endfeature

                {{-- Repository Selection --}}
                <flux:card>
                    <div class="mb-4 flex items-center">
                        <x-heroicon-s-check-circle class="size-5 text-emerald-500" />
                        <span class="ml-2 text-sm font-medium text-emerald-800 dark:text-emerald-200">
                            Connected as <strong>{{ auth()->user()->github_username }}</strong>
                        </span>
                    </div>

                    <flux:heading size="lg">Select Repository</flux:heading>
                    <flux:text class="mt-1">
                        Choose the repository containing your plugin. We'll automatically set up a webhook to keep your plugin in sync.
                    </flux:text>

                    <div class="mt-6">
                        @if($loadingRepos)
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <flux:icon.loading class="size-5" />
                                <span>Loading repositories...</span>
                            </div>
                        @elseif($reposLoaded)
                            <flux:select wire:model.live="repository" label="Repository" placeholder="Select a repository...">
                                @foreach($repositories as $repo)
                                    <flux:select.option value="{{ $repo['full_name'] }}">
                                        {{ $repo['full_name'] }}{{ $repo['private'] ? ' (private)' : '' }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        @endif
                        @error('repository')
                            <flux:text class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </div>
                </flux:card>

                {{-- Paid Plugin Info --}}
                @feature(App\Features\AllowPaidPlugins::class)
                @if($pluginType === 'paid')
                    <flux:card>
                        <flux:heading>How paid plugins work</flux:heading>
                        <ul class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start gap-3">
                                <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                <span>We pull your code from GitHub when you tag a release</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                <span>We host and distribute your plugin via <code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-600">plugins.nativephp.com</code></span>
                            </li>
                            <li class="flex items-start gap-3">
                                <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                <span>Customers install via Composer with their license key</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <x-heroicon-o-check-circle class="mt-0.5 size-5 shrink-0 text-indigo-500" />
                                <span>You get paid automatically via Stripe Connect</span>
                            </li>
                        </ul>
                    </flux:card>
                @endif
                @endfeature

                @if($repository)
                    {{-- Support Channel --}}
                    <flux:card>
                        <flux:heading size="lg">Support Channel</flux:heading>
                        <flux:text class="mt-1">
                            How can users get support for your plugin? Provide an email address or a URL. If you enter a URL, ensure that it clearly details how a visitor goes about getting support for this plugin.
                        </flux:text>

                        <div class="mt-6">
                            <flux:input wire:model="supportChannel" label="Support Channel" placeholder="support@example.com or https://..." />
                        </div>
                    </flux:card>

                    {{-- Notes --}}
                    <flux:card>
                        <flux:heading size="lg">Notes</flux:heading>
                        <flux:text class="mt-1">
                            Any notes for the review team? Feel free to share links to videos of the plugin working. These won't be displayed on your plugin listing.
                        </flux:text>

                        <div class="mt-6">
                            <flux:textarea wire:model="notes" label="Notes" placeholder="Optional notes for the review team..." rows="4" />
                        </div>
                    </flux:card>
                @endif

                {{-- Submit Button --}}
                <div class="flex items-center justify-end gap-4">
                    <flux:button variant="ghost" href="{{ route('customer.plugins.index') }}">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">Submit Plugin</flux:button>
                </div>
            </form>
        @endif
    </div>
</div>
