<div>
    @if($team->is_suspended)
        <div class="mb-6 rounded-lg border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
            <div class="flex">
                <div class="shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        Your team is currently suspended. Reactivate your Ultra subscription to restore team benefits.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Invite Form --}}
    @if(!$team->is_suspended)
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Invite a Team Member</h3>
            <form method="POST" action="{{ route('customer.team.invite') }}" class="mt-4 flex gap-4">
                @csrf
                <div class="flex-1">
                    <input
                        type="email"
                        name="email"
                        placeholder="email@example.com"
                        required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    >
                </div>
                <button
                    type="submit"
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Send Invite
                </button>
            </form>
            @error('email')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>
    @endif

    {{-- Member Count --}}
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Team Members
            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                ({{ $activeMembers->count() }} / 10 seats)
            </span>
        </h3>
    </div>

    {{-- Active Members --}}
    @if($activeMembers->isNotEmpty())
        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($activeMembers as $member)
                    <li class="px-4 py-4" wire:key="member-{{ $member->id }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $member->user?->display_name ?? $member->email }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $member->email }}
                                </p>
                                @if($member->accepted_at)
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        Joined {{ $member->accepted_at->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('customer.team.users.remove', $member) }}" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pending Invitations --}}
    @if($pendingInvitations->isNotEmpty())
        <div class="mt-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-white">
                Pending Invitations
                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                    ({{ $pendingInvitations->count() }})
                </span>
            </h3>
            <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pendingInvitations as $invitation)
                        <li class="px-4 py-4" wire:key="invitation-{{ $invitation->id }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $invitation->email }}
                                    </p>
                                    @if($invitation->invited_at)
                                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                            Invited {{ $invitation->invited_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <form method="POST" action="{{ route('customer.team.users.resend', $invitation) }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                            Resend
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('customer.team.users.remove', $invitation) }}" onsubmit="return confirm('Cancel this invitation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if($activeMembers->isEmpty() && $pendingInvitations->isEmpty())
        <div class="rounded-lg bg-white p-8 text-center shadow dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">No team members yet. Invite someone to get started.</p>
        </div>
    @endif
</div>
