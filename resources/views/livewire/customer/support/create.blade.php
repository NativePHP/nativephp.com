<div>
    <div class="mb-6">
        <a href="{{ route('customer.support.tickets') }}" class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400">
            <x-heroicon-s-arrow-left class="size-4" />
            <span class="font-medium">Support Tickets</span>
        </a>
        <flux:heading size="xl" class="mt-4">Submit a Request</flux:heading>
        <flux:text>We'll get back to you as soon as possible.</flux:text>
    </div>

    <div class="mx-auto max-w-2xl">
        {{-- Step Indicator --}}
        <flux:timeline horizontal size="lg" class="mb-10 justify-center">
            @foreach ([1 => 'Product', 2 => 'Details', 3 => 'Review'] as $step => $label)
                <flux:timeline.item :status="$currentStep > $step ? 'complete' : ($currentStep === $step ? 'current' : 'incomplete')">
                    <flux:timeline.indicator>{{ $step }}</flux:timeline.indicator>
                    <flux:timeline.content>
                        <flux:text>{{ $label }}</flux:text>
                    </flux:timeline.content>
                </flux:timeline.item>
            @endforeach
        </flux:timeline>

        <flux:card class="p-6 md:p-8">
            <form wire:submit="submit">
                {{-- Step 1: Product Selection --}}
                @if ($currentStep === 1)
                    <div>
                        <flux:heading size="lg">Which product is this about?</flux:heading>
                        <flux:text class="mb-6">Select the product related to your request.</flux:text>

                        @error('selectedProduct')
                            <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <flux:radio.group wire:model.live="selectedProduct" variant="cards" class="flex-col">
                            @foreach ([
                                'mobile' => ['label' => 'Mobile', 'desc' => 'iOS & Android apps'],
                                'desktop' => ['label' => 'Desktop', 'desc' => 'macOS, Windows & Linux apps'],
                                'nativephp.com' => ['label' => 'nativephp.com', 'desc' => 'Website, account & billing'],
                            ] as $value => $product)
                                <flux:radio value="{{ $value }}" label="{{ $product['label'] }}" description="{{ $product['desc'] }}" />
                            @endforeach
                        </flux:radio.group>
                    </div>
                @endif

                {{-- Step 2: Context Questions --}}
                @if ($currentStep === 2)
                    <div class="space-y-8">
                        {{-- Mobile Area --}}
                        @if ($this->showMobileArea)
                            <div>
                                <flux:heading size="lg">What is the issue related to?</flux:heading>
                                <flux:text class="mb-4">Is this about NativePHP for Mobile itself, or a specific plugin/tool?</flux:text>

                                <flux:radio.group wire:model.live="mobileAreaType" variant="cards" class="flex-col sm:flex-row">
                                    <flux:radio value="core" label="NativePHP Mobile" description="The core framework" />
                                    <flux:radio value="plugin" label="Plugin / Tool" description="A specific plugin or tool" />
                                </flux:radio.group>

                                @error('mobileAreaType')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                @if ($mobileAreaType === 'plugin')
                                    <div class="mt-4">
                                        <flux:select wire:model.live="mobileArea" label="Which plugin or tool?" placeholder="Select a plugin or tool...">
                                            @foreach ($officialPlugins as $id => $pluginName)
                                                <flux:select.option value="{{ $pluginName }}">{{ $pluginName }}</flux:select.option>
                                            @endforeach
                                            <flux:select.option value="jump">Jump</flux:select.option>
                                            <flux:select.option value="other">Other</flux:select.option>
                                        </flux:select>
                                        @error('mobileArea')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Bug Report Fields --}}
                        @if ($this->showBugReportFields)
                            <div>
                                <flux:heading size="lg">Bug report details</flux:heading>
                                <flux:text class="mb-4">Help us understand and reproduce the issue.</flux:text>

                                <div class="space-y-4">
                                    <flux:textarea
                                        wire:model="tryingToDo"
                                        label="What were you trying to do?"
                                        rows="3"
                                        placeholder="Describe the goal you were trying to achieve..."
                                    />

                                    <flux:textarea
                                        wire:model="whatHappened"
                                        label="What happened instead?"
                                        rows="3"
                                        placeholder="Describe the unexpected behavior..."
                                    />

                                    <flux:textarea
                                        wire:model="reproductionSteps"
                                        label="Steps to reproduce"
                                        rows="4"
                                        placeholder="1. Open the app&#10;2. Navigate to...&#10;3. Click on..."
                                    />

                                    <flux:field>
                                        <flux:label>Environment</flux:label>
                                        <flux:description>
                                            Run <code class="rounded bg-zinc-100 px-1.5 py-0.5 font-mono text-violet-600 dark:bg-zinc-800 dark:text-violet-400">php artisan native:debug</code> in your project and paste the output here.
                                        </flux:description>
                                        <flux:textarea
                                            wire:model="environment"
                                            rows="4"
                                            placeholder="Paste the output of `php artisan native:debug` here..."
                                        />
                                        <flux:error name="environment" />
                                    </flux:field>
                                </div>
                            </div>
                        @endif

                        {{-- Issue Type --}}
                        @if ($this->showIssueType)
                            <div>
                                <flux:heading size="lg">Issue type</flux:heading>
                                <flux:text class="mb-4">What kind of issue are you experiencing?</flux:text>

                                <flux:radio.group wire:model.live="issueType" variant="cards" class="flex-col">
                                    <flux:radio value="account_query" label="Account query" />
                                    <flux:radio value="bug" label="Bug" />
                                    <flux:radio value="feature_request" label="Feature request" />
                                    <flux:radio value="other" label="Other" />
                                </flux:radio.group>

                                @error('issueType')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Subject + Message (only for non-bug-report flows) --}}
                        @if (! $this->showBugReportFields)
                            <div>
                                <flux:heading size="lg">Describe your issue</flux:heading>
                                <flux:text class="mb-4">Provide a summary and any additional details.</flux:text>

                                <div class="space-y-4">
                                    <flux:input
                                        wire:model="subject"
                                        label="Subject"
                                        placeholder="Brief description of your issue"
                                    />

                                    <flux:textarea
                                        wire:model="message"
                                        label="Message"
                                        rows="6"
                                        placeholder="Describe your issue in detail..."
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Step 3: Review & Submit --}}
                @if ($currentStep === 3)
                    <div>
                        <flux:heading size="lg">Review your request</flux:heading>
                        <flux:text class="mb-6">Please review the details below before submitting.</flux:text>

                        <div class="space-y-4">
                            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Product</dt>
                                <dd class="mt-1 text-zinc-900 dark:text-white">
                                    {{ ['mobile' => 'Mobile', 'desktop' => 'Desktop', 'bifrost' => 'Bifrost', 'nativephp.com' => 'nativephp.com'][$selectedProduct] ?? $selectedProduct }}
                                </dd>
                            </div>

                            @if ($mobileAreaType)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Area</dt>
                                    <dd class="mt-1 text-zinc-900 dark:text-white">
                                        {{ $mobileAreaType === 'core' ? 'NativePHP Mobile (core)' : $mobileArea }}
                                    </dd>
                                </div>
                            @endif

                            @if ($issueType)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Issue type</dt>
                                    <dd class="mt-1 text-zinc-900 dark:text-white">
                                        {{ ['account_query' => 'Account query', 'bug' => 'Bug', 'feature_request' => 'Feature request', 'other' => 'Other'][$issueType] ?? $issueType }}
                                    </dd>
                                </div>
                            @endif

                            @if (! $this->showBugReportFields)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Subject</dt>
                                    <dd class="mt-1 text-zinc-900 dark:text-white">{{ $subject }}</dd>
                                </div>

                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Message</dt>
                                    <dd class="mt-1 whitespace-pre-line text-zinc-900 dark:text-white">{{ $message }}</dd>
                                </div>
                            @endif

                            @if ($tryingToDo)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">What you were trying to do</dt>
                                    <dd class="mt-1 whitespace-pre-line text-zinc-900 dark:text-white">{{ $tryingToDo }}</dd>
                                </div>
                            @endif

                            @if ($whatHappened)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">What happened instead</dt>
                                    <dd class="mt-1 whitespace-pre-line text-zinc-900 dark:text-white">{{ $whatHappened }}</dd>
                                </div>
                            @endif

                            @if ($reproductionSteps)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Steps to reproduce</dt>
                                    <dd class="mt-1 whitespace-pre-line text-zinc-900 dark:text-white">{{ $reproductionSteps }}</dd>
                                </div>
                            @endif

                            @if ($environment)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Environment</dt>
                                    <dd class="mt-1 whitespace-pre-line font-mono text-sm text-zinc-900 dark:text-white">{{ $environment }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Navigation --}}
                <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
                    @if ($currentStep > 1)
                        <flux:button type="button" wire:click="previousStep" icon="arrow-left">
                            Back
                        </flux:button>
                    @else
                        <div></div>
                    @endif

                    @if ($currentStep < 3)
                        <flux:button type="button" wire:click="nextStep" variant="primary" icon-trailing="arrow-right">
                            Continue
                        </flux:button>
                    @else
                        <flux:button type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="submit">Submit Request</span>
                            <span wire:loading wire:target="submit">Submitting...</span>
                        </flux:button>
                    @endif
                </div>
            </form>
        </flux:card>
    </div>
</div>
