<div class="mt-8 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
    @if ($submitted)
        <p class="text-sm text-gray-600 dark:text-gray-400">Thanks for the feedback!</p>
    @else
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Was this page helpful?</span>

            <flux:button size="sm" variant="{{ $helpful === true ? 'primary' : 'outline' }}" wire:click="vote(true)">
                Yes
            </flux:button>

            <flux:button size="sm" variant="{{ $helpful === false ? 'primary' : 'outline' }}" wire:click="vote(false)">
                No
            </flux:button>
        </div>

        <flux:error name="helpful" />

        @if (! is_null($helpful))
            <div class="mt-4 space-y-3">
                <flux:field>
                    <flux:label>What were you looking for? (optional)</flux:label>
                    <flux:textarea wire:model="comment" rows="3" />
                    <flux:error name="comment" />
                </flux:field>

                <flux:error name="form" />

                <flux:button size="sm" variant="primary" wire:click="submit">
                    Send feedback
                </flux:button>
            </div>
        @endif
    @endif
</div>
