<x-layouts.dashboard title="Team">
    <div>
        <div class="mb-6" x-data="{ editingName: {{ $errors->has('name') ? 'true' : 'false' }} }">
            @if($team)
                <div class="flex items-center gap-3">
                    <flux:heading size="xl">{{ $team->name }}</flux:heading>
                    <flux:button variant="ghost" size="sm" icon="pencil" x-show="!editingName" x-on:click="editingName = true" />
                </div>
                <flux:text>Manage your team and share your Ultra benefits</flux:text>

                {{-- Inline Team Name Edit --}}
                <div x-show="editingName" x-cloak class="mt-4">
                    <form method="POST" action="{{ route('customer.team.update') }}" class="flex items-start gap-3">
                        @csrf
                        @method('PATCH')
                        <div class="flex-1">
                            <flux:input name="name" :value="old('name', $team->name)" placeholder="Team name" />
                            @error('name')
                                <flux:text class="mt-1 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                            @enderror
                        </div>
                        <flux:button type="submit" variant="primary">Save</flux:button>
                        <flux:button type="button" variant="ghost" x-on:click="editingName = false">Cancel</flux:button>
                    </form>
                </div>
            @else
                <flux:heading size="xl">Team</flux:heading>
                <flux:text>Manage your team and share your Ultra benefits</flux:text>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>{{ session('success') }}</flux:callout.text>
            </flux:callout>
        @endif

        @if(session('error'))
            <flux:callout variant="danger" icon="x-circle" class="mb-6">
                <flux:callout.text>{{ session('error') }}</flux:callout.text>
            </flux:callout>
        @endif

        <div class="max-w-3xl">
            @if($team)
                {{-- User owns a team --}}
                <livewire:team-manager :team="$team" />
            @elseif($membership)
                {{-- User is a member of another team --}}
                <flux:card>
                    <flux:heading size="lg">Team Membership</flux:heading>
                    <flux:text class="mt-2">
                        You are a member of <strong>{{ $membership->team->name }}</strong>.
                    </flux:text>
                    <div class="mt-4">
                        <flux:heading size="sm">Your benefits include:</flux:heading>
                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                            <li>Free access to all first-party NativePHP plugins</li>
                            <li>Access to the Plugin Dev Kit GitHub repository</li>
                        </ul>
                    </div>
                </flux:card>
            @elseif(auth()->user()->hasActiveUltraSubscription())
                {{-- User has Ultra but no team --}}
                <flux:card>
                    <flux:heading size="lg">Create a Team</flux:heading>
                    <flux:text class="mb-4">As an Ultra subscriber, you can create a team and invite up to 10 members who will share your benefits.</flux:text>

                    <form method="POST" action="{{ route('customer.team.store') }}">
                        @csrf
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <flux:input name="name" placeholder="Team name" required />
                            </div>
                            <flux:button type="submit" variant="primary">Create Team</flux:button>
                        </div>
                        @error('name')
                            <flux:text class="mt-2 text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                        @enderror
                    </form>
                </flux:card>
            @else
                {{-- User doesn't have Ultra --}}
                <flux:card>
                    <flux:heading size="lg">Teams</flux:heading>
                    <flux:text class="mb-4">Teams are available to Ultra subscribers. Upgrade to Ultra to create a team and share benefits with up to 10 members.</flux:text>

                    <flux:button variant="primary" :href="route('pricing')">View Plans</flux:button>
                </flux:card>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
