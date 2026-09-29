<?php

namespace App\Livewire\Customer;

use App\Models\EmailChange;
use App\Notifications\EmailChangeRequested;
use App\Notifications\VerifyNewEmailAddress;
use Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.dashboard')]
#[Title('Settings')]
class Settings extends Component
{
    #[Url]
    public string $tab = 'account';

    public string $name = '';

    public string $newEmail = '';

    public string $emailChangePassword = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    public string $deleteConfirmPassword = '';

    public bool $receivesNotificationEmails = true;

    public bool $receivesNewPluginNotifications = true;

    public function mount(): void
    {
        $this->name = auth()->user()->name ?? '';
        $this->receivesNotificationEmails = auth()->user()->receives_notification_emails;
        $this->receivesNewPluginNotifications = auth()->user()->receives_new_plugin_notifications;
    }

    public function updatedReceivesNotificationEmails(bool $value): void
    {
        auth()->user()->update(['receives_notification_emails' => $value]);
    }

    public function updatedReceivesNewPluginNotifications(bool $value): void
    {
        auth()->user()->update(['receives_new_plugin_notifications' => $value]);
    }

    #[Computed]
    public function pendingEmailChange(): ?EmailChange
    {
        return auth()->user()->emailChanges()->pending()->latest()->first();
    }

    public function resetEmailChangeForm(): void
    {
        $this->reset('newEmail', 'emailChangePassword');
        $this->resetValidation(['newEmail', 'emailChangePassword']);
    }

    public function requestEmailChange(): void
    {
        $this->validate([
            'newEmail' => [
                'required',
                'string',
                'email',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (strcasecmp($value, auth()->user()->email) === 0) {
                        $fail('This is already your email address.');
                    }
                },
                'unique:users,email',
            ],
            'emailChangePassword' => ['required', 'current_password'],
        ]);

        $user = auth()->user();
        $rateLimiterKey = 'email-change:'.$user->id;

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 3)) {
            $this->addError('newEmail', 'Too many email change requests. Please try again later.');

            return;
        }

        RateLimiter::hit($rateLimiterKey, 600);

        $user->emailChanges()->whereNull('confirmed_at')->delete();

        $emailChange = $user->emailChanges()->create([
            'old_email' => $user->email,
            'new_email' => $this->newEmail,
            'ip_address' => request()->ip(),
            'expires_at' => now()->addHour(),
        ]);

        Notification::route('mail', $emailChange->new_email)->notify(new VerifyNewEmailAddress($emailChange));
        $user->notify(new EmailChangeRequested($emailChange));

        $this->reset('newEmail', 'emailChangePassword');
        unset($this->pendingEmailChange);

        Flux::modal('change-email')->close();

        session()->flash('email-change-requested', "We've sent a confirmation link to {$emailChange->new_email}. Your email will change once you click it.");
    }

    public function resendEmailChange(): void
    {
        $emailChange = $this->pendingEmailChange;

        if (! $emailChange) {
            return;
        }

        $rateLimiterKey = 'email-change:'.auth()->id();

        if (RateLimiter::tooManyAttempts($rateLimiterKey, 3)) {
            $this->addError('newEmail', 'Too many email change requests. Please try again later.');

            return;
        }

        RateLimiter::hit($rateLimiterKey, 600);

        Notification::route('mail', $emailChange->new_email)->notify(new VerifyNewEmailAddress($emailChange));

        session()->flash('email-change-requested', "We've re-sent the confirmation link to {$emailChange->new_email}.");
    }

    public function cancelEmailChange(): void
    {
        auth()->user()->emailChanges()->whereNull('confirmed_at')->delete();

        unset($this->pendingEmailChange);

        session()->flash('email-change-cancelled', 'The email change has been cancelled.');
    }

    public function updateName(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        auth()->user()->update(['name' => $this->name]);

        session()->flash('name-updated', 'Your name has been updated.');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password'],
            'newPassword' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->newPassword),
        ]);

        $this->reset('currentPassword', 'newPassword', 'newPassword_confirmation');

        session()->flash('password-updated', 'Your password has been updated.');
    }

    public function deleteAccount(): void
    {
        $this->validate([
            'deleteConfirmPassword' => ['required', 'current_password'],
        ]);

        $user = auth()->user();

        // Cancel active subscription immediately
        $subscription = $user->subscription();

        if ($subscription && $subscription->active()) {
            $subscription->cancelNow();
        }

        // Delete related records that lack cascade foreign keys
        $user->licenses()->delete();
        $user->subscriptions()->delete();

        Auth::guard('web')->logout();

        $user->delete();

        $this->redirect(route('welcome'));
    }
}
