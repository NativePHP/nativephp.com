@use('Illuminate\Support\Facades\Storage')

<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Your Showcase Submissions</flux:heading>
            <flux:text>Submit your NativePHP apps to be featured on our showcase</flux:text>
        </div>
        <flux:button variant="primary" href="{{ route('customer.showcase.create') }}">Submit New App</flux:button>
    </div>

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

    @if($this->showcases->count() > 0)
        <flux:table>
            <flux:table.columns>
                <flux:table.column>App</flux:table.column>
                <flux:table.column>Platforms</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->showcases as $showcase)
                    <flux:table.row :key="$showcase->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-4">
                                @if($showcase->image)
                                    <img src="{{ Storage::disk('public')->url($showcase->image) }}" alt="{{ $showcase->title }}" class="size-10 rounded-lg object-cover shrink-0">
                                @else
                                    <div class="size-10 rounded-lg bg-zinc-200 dark:bg-zinc-700 shrink-0 flex items-center justify-center">
                                        <flux:icon name="photo" class="size-5 text-zinc-400" />
                                    </div>
                                @endif
                                <a href="{{ route('customer.showcase.edit', $showcase) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $showcase->title }}
                                </a>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex gap-1">
                                @if($showcase->has_mobile)
                                    <flux:badge color="purple" size="sm">Mobile</flux:badge>
                                @endif
                                @if($showcase->has_desktop)
                                    <flux:badge color="indigo" size="sm">Desktop</flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <x-customer.status-badge :status="$showcase->isApproved() ? 'Approved' : 'Pending Review'" />
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:button size="sm" href="{{ route('customer.showcase.edit', $showcase) }}">Edit</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @else
        <x-customer.empty-state
            icon="squares-2x2"
            title="No submissions yet"
            description="Get started by submitting your first NativePHP app to the showcase."
        >
            <flux:button variant="primary" href="{{ route('customer.showcase.create') }}">Submit Your App</flux:button>
        </x-customer.empty-state>
    @endif
</div>
