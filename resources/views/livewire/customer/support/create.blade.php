{{-- Step Indicator --}}
<div>
    <div class="mx-auto max-w-2xl">
        <div class="mb-10 flex items-center justify-center">
            @for ($i = 1; $i <= 3; $i++)
                <div class="flex items-center">
                    <div
                        @class([
                            'flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium transition-colors duration-200',
                            'bg-violet-600 text-white' => $currentStep >= $i,
                            'bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400' => $currentStep < $i,
                        ])
                    >
                        @if ($currentStep > $i)
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    @if ($i < 3)
                        <div
                            @class([
                                'mx-2 h-0.5 w-12 transition-colors duration-200',
                                'bg-violet-600' => $currentStep > $i,
                                'bg-gray-200 dark:bg-gray-700' => $currentStep <= $i,
                            ])
                        ></div>
                    @endif
                </div>
            @endfor
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-700 dark:bg-gray-900">
            <form wire:submit="submit">
                {{-- Step 1: Product Selection --}}
                @if ($currentStep === 1)
                    <div>
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Which product is this about?</h2>
                        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Select the product related to your request.</p>

                        @error('selectedProduct')
                            <p class="mb-4 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ([
                                'mobile' => ['label' => 'Mobile', 'desc' => 'iOS & Android apps'],
                                'desktop' => ['label' => 'Desktop', 'desc' => 'macOS, Windows & Linux apps'],
                                {{-- 'bifrost' => ['label' => 'Bifrost', 'desc' => 'Build & deployment service'], --}}
                                'nativephp.com' => ['label' => 'nativephp.com', 'desc' => 'Website, account & billing'],
                            ] as $value => $product)
                                <label
                                    @class([
                                        'flex cursor-pointer items-center gap-3 rounded-lg border-2 p-4 transition-colors duration-200',
                                        'border-violet-600 bg-violet-50 dark:border-violet-500 dark:bg-violet-900/20' => $selectedProduct === $value,
                                        'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600' => $selectedProduct !== $value,
                                    ])
                                >
                                    <input type="radio" wire:model.live="selectedProduct" value="{{ $value }}" class="sr-only" />
                                    <div
                                        @class([
                                            'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors duration-200',
                                            'border-violet-600 dark:border-violet-500' => $selectedProduct === $value,
                                            'border-gray-300 dark:border-gray-600' => $selectedProduct !== $value,
                                        ])
                                    >
                                        @if ($selectedProduct === $value)
                                            <div class="h-2.5 w-2.5 rounded-full bg-violet-600 dark:bg-violet-500"></div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="block font-medium text-gray-900 dark:text-white">{{ $product['label'] }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $product['desc'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Step 2: Context Questions --}}
                @if ($currentStep === 2)
                    <div class="space-y-8">
                        {{-- Mobile Area --}}
                        @if ($this->showMobileArea)
                            <div>
                                <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">What is the issue related to?</h2>
                                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Is this about NativePHP for Mobile itself, or a specific plugin/tool?</p>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach ([
                                        'core' => ['label' => 'NativePHP Mobile', 'desc' => 'The core framework'],
                                        'plugin' => ['label' => 'Plugin / Tool', 'desc' => 'A specific plugin or tool'],
                                    ] as $value => $option)
                                        <label
                                            @class([
                                                'flex cursor-pointer items-center gap-3 rounded-lg border-2 p-4 transition-colors duration-200',
                                                'border-violet-600 bg-violet-50 dark:border-violet-500 dark:bg-violet-900/20' => $mobileAreaType === $value,
                                                'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600' => $mobileAreaType !== $value,
                                            ])
                                        >
                                            <input type="radio" wire:model.live="mobileAreaType" value="{{ $value }}" class="sr-only" />
                                            <div
                                                @class([
                                                    'flex h-5 w-5 shrink-0 items-center justify-center rounded-full border-2 transition-colors duration-200',
                                                    'border-violet-600 dark:border-violet-500' => $mobileAreaType === $value,
                                                    'border-gray-300 dark:border-gray-600' => $mobileAreaType !== $value,
                                                ])
                                            >
                                                @if ($mobileAreaType === $value)
                                                    <div class="h-2.5 w-2.5 rounded-full bg-violet-600 dark:bg-violet-500"></div>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="block font-medium text-gray-900 dark:text-white">{{ $option['label'] }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $option['desc'] }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('mobileAreaType')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                @if ($mobileAreaType === 'plugin')
                                    <div class="mt-4">
                                        <label for="mobileArea" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Which plugin or tool?
                                        </label>
                                        <select
                                            id="mobileArea"
                                            wire:model.live="mobileArea"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                        >
                                            <option value="">Select a plugin or tool...</option>
                                            @foreach ($officialPlugins as $id => $pluginName)
                                                <option value="{{ $pluginName }}">{{ $pluginName }}</option>
                                            @endforeach
                                            <option value="jump">Jump</option>
                                            <option value="other">Other</option>
                                        </select>
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
                                <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Bug report details</h2>
                                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Help us understand and reproduce the issue.</p>

                                <div class="space-y-4">
                                    <div>
                                        <label for="tryingToDo" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            What were you trying to do?
                                        </label>
                                        <textarea
                                            id="tryingToDo"
                                            wire:model="tryingToDo"
                                            rows="3"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="Describe the goal you were trying to achieve..."
                                        ></textarea>
                                        @error('tryingToDo')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="whatHappened" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            What happened instead?
                                        </label>
                                        <textarea
                                            id="whatHappened"
                                            wire:model="whatHappened"
                                            rows="3"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="Describe the unexpected behavior..."
                                        ></textarea>
                                        @error('whatHappened')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="reproductionSteps" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Steps to reproduce
                                        </label>
                                        <textarea
                                            id="reproductionSteps"
                                            wire:model="reproductionSteps"
                                            rows="4"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="1. Open the app&#10;2. Navigate to...&#10;3. Click on..."
                                        ></textarea>
                                        @error('reproductionSteps')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="environment" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Environment
                                        </label>
                                        <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                            Run <code class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-violet-600 dark:bg-gray-800 dark:text-violet-400">php artisan native:debug</code> in your project and paste the output here.
                                        </p>
                                        <textarea
                                            id="environment"
                                            wire:model="environment"
                                            rows="4"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="Paste the output of `php artisan native:debug` here..."
                                        ></textarea>
                                        @error('environment')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Issue Type --}}
                        @if ($this->showIssueType)
                            <div>
                                <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Issue type</h2>
                                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">What kind of issue are you experiencing?</p>

                                <div class="space-y-2">
                                    @foreach ([
                                        'account_query' => 'Account query',
                                        'bug' => 'Bug',
                                        'feature_request' => 'Feature request',
                                        'other' => 'Other',
                                    ] as $value => $label)
                                        <label
                                            @class([
                                                'flex cursor-pointer items-center gap-3 rounded-lg border-2 p-3 transition-colors duration-200',
                                                'border-violet-600 bg-violet-50 dark:border-violet-500 dark:bg-violet-900/20' => $issueType === $value,
                                                'border-gray-200 hover:border-gray-300 dark:border-gray-700 dark:hover:border-gray-600' => $issueType !== $value,
                                            ])
                                        >
                                            <input type="radio" wire:model.live="issueType" value="{{ $value }}" class="sr-only" />
                                            <div
                                                @class([
                                                    'flex h-4 w-4 shrink-0 items-center justify-center rounded-full border-2 transition-colors duration-200',
                                                    'border-violet-600 dark:border-violet-500' => $issueType === $value,
                                                    'border-gray-300 dark:border-gray-600' => $issueType !== $value,
                                                ])
                                            >
                                                @if ($issueType === $value)
                                                    <div class="h-2 w-2 rounded-full bg-violet-600 dark:bg-violet-500"></div>
                                                @endif
                                            </div>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>

                                @error('issueType')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- Subject + Message (only for non-bug-report flows) --}}
                        @if (! $this->showBugReportFields)
                            <div>
                                <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Describe your issue</h2>
                                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">Provide a summary and any additional details.</p>

                                <div class="space-y-4">
                                    <div>
                                        <label for="subject" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Subject
                                        </label>
                                        <input
                                            type="text"
                                            id="subject"
                                            wire:model="subject"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="Brief description of your issue"
                                        />
                                        @error('subject')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="message" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Message
                                        </label>
                                        <textarea
                                            id="message"
                                            wire:model="message"
                                            rows="6"
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-violet-500 focus:ring-violet-500 sm:text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                            placeholder="Describe your issue in detail..."
                                        ></textarea>
                                        @error('message')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Step 3: Review & Submit --}}
                @if ($currentStep === 3)
                    <div>
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Review your request</h2>
                        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">Please review the details below before submitting.</p>

                        <div class="space-y-4">
                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Product</dt>
                                <dd class="mt-1 text-gray-900 dark:text-white">
                                    {{ ['mobile' => 'Mobile', 'desktop' => 'Desktop', 'bifrost' => 'Bifrost', 'nativephp.com' => 'nativephp.com'][$selectedProduct] ?? $selectedProduct }}
                                </dd>
                            </div>

                            @if ($mobileAreaType)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Area</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">
                                        {{ $mobileAreaType === 'core' ? 'NativePHP Mobile (core)' : $mobileArea }}
                                    </dd>
                                </div>
                            @endif

                            @if ($issueType)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Issue type</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">
                                        {{ ['account_query' => 'Account query', 'bug' => 'Bug', 'feature_request' => 'Feature request', 'other' => 'Other'][$issueType] ?? $issueType }}
                                    </dd>
                                </div>
                            @endif

                            @if (! $this->showBugReportFields)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Subject</dt>
                                    <dd class="mt-1 text-gray-900 dark:text-white">{{ $subject }}</dd>
                                </div>

                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Message</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-white">{{ $message }}</dd>
                                </div>
                            @endif

                            @if ($tryingToDo)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">What you were trying to do</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-white">{{ $tryingToDo }}</dd>
                                </div>
                            @endif

                            @if ($whatHappened)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">What happened instead</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-white">{{ $whatHappened }}</dd>
                                </div>
                            @endif

                            @if ($reproductionSteps)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Steps to reproduce</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900 dark:text-white">{{ $reproductionSteps }}</dd>
                                </div>
                            @endif

                            @if ($environment)
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
                                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Environment</dt>
                                    <dd class="mt-1 whitespace-pre-line font-mono text-sm text-gray-900 dark:text-white">{{ $environment }}</dd>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Navigation --}}
                <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-between">
                    @if ($currentStep === 1)
                        <a href="{{ route('customer.support.tickets') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Tickets
                        </a>
                    @else
                        <button type="button" wire:click="previousStep" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition duration-200 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back
                        </button>
                    @endif

                    @if ($currentStep < 3)
                        <button type="button" wire:click="nextStep" class="inline-flex items-center justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-violet-700 dark:bg-violet-700 dark:hover:bg-violet-600">
                            Continue
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    @else
                        <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition duration-200 hover:bg-violet-700 disabled:opacity-50 dark:bg-violet-700 dark:hover:bg-violet-600">
                            <span wire:loading.remove wire:target="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Submit Request
                            </span>
                            <span wire:loading wire:target="submit">
                                <svg class="mr-2 inline h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
