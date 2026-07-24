<div class="mx-auto max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">Settings</flux:heading>
        <flux:text>Manage your account settings.</flux:text>
    </div>

    <div class="mb-6 flex gap-2 border-b border-zinc-200 dark:border-zinc-700">
        <button
            wire:click="$set('tab', 'account')"
            class="{{ $tab === 'account' ? 'border-b-2 border-zinc-800 dark:border-white text-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-3 pb-2 text-sm font-medium transition-colors"
        >
            Account
        </button>
        <button
            wire:click="$set('tab', 'notifications')"
            class="{{ $tab === 'notifications' ? 'border-b-2 border-zinc-800 dark:border-white text-zinc-800 dark:text-white' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-300' }} px-3 pb-2 text-sm font-medium transition-colors"
        >
            Notifications
        </button>
    </div>

    @if ($tab === 'account')
        {{-- Update Name --}}
        <flux:card class="mb-6">
            <form wire:submit="updateName">
                <flux:heading size="lg">Name</flux:heading>
                <flux:text class="mb-4">Update the name associated with your account.</flux:text>

                @if (session('name-updated'))
                    <flux:callout variant="success" icon="check-circle" class="mb-4">
                        <flux:callout.text>{{ session('name-updated') }}</flux:callout.text>
                    </flux:callout>
                @endif

                <div class="mb-4">
                    <flux:input wire:model="name" label="Name" placeholder="Your name" />
                </div>

                <flux:button type="submit" variant="primary">Save</flux:button>
            </form>
        </flux:card>

        {{-- Email Address --}}
        <flux:card class="mb-6">
            <flux:heading size="lg">Email Address</flux:heading>
            <flux:text class="mb-4">The email address associated with your account.</flux:text>

            @if (session('email-changed'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    <flux:callout.text>{{ session('email-changed') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if (session('email-change-failed'))
                <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4">
                    <flux:callout.text>{{ session('email-change-failed') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if (session('email-change-requested'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    <flux:callout.text>{{ session('email-change-requested') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if (session('email-change-cancelled'))
                <flux:callout variant="secondary" icon="information-circle" class="mb-4">
                    <flux:callout.text>{{ session('email-change-cancelled') }}</flux:callout.text>
                </flux:callout>
            @endif

            <div class="mb-4">
                <flux:input label="Current Email" :value="auth()->user()->email" readonly />
            </div>

            @if (auth()->user()->github_id && ! auth()->user()->password)
                <flux:callout variant="warning" icon="information-circle">
                    <flux:callout.text>
                        Your account uses GitHub for authentication. To change your email address, first set a password
                        using the <flux:link href="{{ route('password.request') }}">password reset</flux:link> flow.
                    </flux:callout.text>
                </flux:callout>
            @elseif ($this->pendingEmailChange)
                <flux:callout variant="warning" icon="clock" class="mb-4">
                    <flux:callout.text>
                        We've sent a confirmation link to <strong>{{ $this->pendingEmailChange->new_email }}</strong>.
                        Your email address will only change once you click it. The link expires
                        {{ $this->pendingEmailChange->expires_at->diffForHumans() }}.
                    </flux:callout.text>
                </flux:callout>

                <div class="flex gap-3">
                    <flux:button wire:click="resendEmailChange" variant="ghost">Resend Link</flux:button>
                    <flux:button wire:click="cancelEmailChange" variant="ghost">Cancel Change</flux:button>
                </div>
            @else
                <flux:modal.trigger name="change-email">
                    <flux:button>Change Email Address</flux:button>
                </flux:modal.trigger>
            @endif
        </flux:card>

        {{-- Change Email Confirmation Modal --}}
        <flux:modal name="change-email" class="md:w-[32rem]">
            <form wire:submit="requestEmailChange">
                <flux:heading size="lg">Change Email Address</flux:heading>

                <flux:callout variant="warning" icon="exclamation-triangle" class="mt-4 mb-4">
                    <flux:callout.text>
                        Changing your email affects more than just logging in:
                    </flux:callout.text>
                    <flux:callout.text>
                        &bull; Your plugin repository (Composer) credentials use your email address. Any
                        <code>auth.json</code> files or CI secrets configured with your current address will stop
                        working until you update them to the new one.
                    </flux:callout.text>
                    <flux:callout.text>
                        &bull; Your billing email will be updated, so receipts and invoices go to the new address.
                    </flux:callout.text>
                    <flux:callout.text>
                        We'll send a confirmation link to the new address &mdash; nothing changes until you click it.
                    </flux:callout.text>
                </flux:callout>

                <div class="space-y-4">
                    <flux:input wire:model="newEmail" label="New Email" type="email" placeholder="you@example.com" />
                    <flux:input wire:model="emailChangePassword" label="Current Password" type="password" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost" wire:click="resetEmailChangeForm">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">Send Confirmation Link</flux:button>
                </div>
            </form>
        </flux:modal>

        {{-- Change Password --}}
        <flux:card class="mb-6">
            <flux:heading size="lg">Password</flux:heading>
            <flux:text class="mb-4">Update your password to keep your account secure.</flux:text>

            @if (session('password-updated'))
                <flux:callout variant="success" icon="check-circle" class="mb-4">
                    <flux:callout.text>{{ session('password-updated') }}</flux:callout.text>
                </flux:callout>
            @endif

            @if (auth()->user()->github_id && ! auth()->user()->password)
                <flux:callout variant="warning" icon="information-circle">
                    <flux:callout.text>
                        Your account uses GitHub for authentication. To set a password, use the
                        <flux:link href="{{ route('password.request') }}">password reset</flux:link> flow.
                    </flux:callout.text>
                </flux:callout>
            @else
                <form wire:submit="updatePassword">
                    <div class="mb-4 space-y-4">
                        <flux:input wire:model="currentPassword" label="Current Password" type="password" />
                        <flux:input wire:model="newPassword" label="New Password" type="password" />
                        <flux:input wire:model="newPassword_confirmation" label="Confirm New Password" type="password" />
                    </div>

                    <flux:button type="submit" variant="primary">Update Password</flux:button>
                </form>
            @endif
        </flux:card>

        {{-- Delete Account --}}
        <flux:card>
            <flux:heading size="lg">Delete Account</flux:heading>
            <flux:text class="mb-4">Permanently delete your account and all associated data.</flux:text>

            <flux:callout variant="danger" icon="exclamation-triangle" class="mb-4">
                <flux:callout.text>
                    This action is irreversible. All your licenses and data will be permanently removed.
                    @if (auth()->user()->subscription()?->active())
                        Your active subscription will also be cancelled immediately.
                    @endif
                </flux:callout.text>
            </flux:callout>

            <flux:modal.trigger name="delete-account">
                <flux:button variant="danger">Delete Account</flux:button>
            </flux:modal.trigger>
        </flux:card>

        {{-- Delete Account Confirmation Modal --}}
        <flux:modal name="delete-account">
            <form wire:submit="deleteAccount">
                <flux:heading size="lg">Confirm Account Deletion</flux:heading>

                <flux:text class="mt-2">
                    Please enter your password to confirm you want to permanently delete your account.
                </flux:text>

                <div class="mt-4">
                    <flux:input
                        wire:model="deleteConfirmPassword"
                        label="Password"
                        type="password"
                        placeholder="Enter your password"
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="danger">Delete My Account</flux:button>
                </div>
            </form>
        </flux:modal>
    @endif

    @if ($tab === 'notifications')
        @if (session('new-plugin-notifications-disabled'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>New plugin notifications have been disabled.</flux:callout.text>
            </flux:callout>
        @endif

        @if (session('new-plugin-notifications-enabled'))
            <flux:callout variant="success" icon="check-circle" class="mb-6">
                <flux:callout.text>New plugin notifications have been re-enabled.</flux:callout.text>
            </flux:callout>
        @endif

        <flux:card class="space-y-6">
            <flux:switch
                wire:model.live="receivesNotificationEmails"
                label="Email notifications"
                description="Receive email notifications about your account activity."
            />

            <flux:switch
                wire:model.live="receivesNewPluginNotifications"
                label="New plugin notifications"
                description="Get notified when new plugins are added to the directory."
            />
        </flux:card>
    @endif
</div>
