<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Support Tickets</flux:heading>
            <flux:text>Manage your support tickets</flux:text>
        </div>
        <flux:button variant="primary" href="{{ route('customer.support.tickets.create') }}">Submit a new request</flux:button>
    </div>

    @if(session()->has('success'))
        <flux:callout variant="success" icon="check-circle" class="mb-6">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if($this->supportTickets->count() > 0)
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Ticket ID</flux:table.column>
                <flux:table.column>Subject</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->supportTickets as $ticket)
                    <flux:table.row :key="$ticket->id">
                        <flux:table.cell>
                            <a href="{{ route('customer.support.tickets.show', $ticket) }}" class="font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                #{{ $ticket->mask }}
                            </a>
                        </flux:table.cell>

                        <flux:table.cell>{{ $ticket->subject }}</flux:table.cell>

                        <flux:table.cell>
                            <x-customer.status-badge :status="$ticket->status->translated()" />
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:button size="sm" href="{{ route('customer.support.tickets.show', $ticket) }}">View</flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @if($this->supportTickets->hasPages())
            <div class="mt-4">
                {{ $this->supportTickets->links() }}
            </div>
        @endif
    @else
        <x-customer.empty-state
            icon="chat-bubble-left-right"
            title="No tickets yet"
            description="Submit a support ticket and we'll get back to you as soon as possible."
        >
            <flux:button variant="primary" href="{{ route('customer.support.tickets.create') }}">Submit a new request</flux:button>
        </x-customer.empty-state>
    @endif
</div>
