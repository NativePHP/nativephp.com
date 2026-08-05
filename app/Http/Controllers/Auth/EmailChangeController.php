<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SyncStripeCustomerDetailsJob;
use App\Models\EmailChange;
use App\Models\TeamUser;
use App\Models\User;
use App\Notifications\EmailChangeCompleted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class EmailChangeController extends Controller
{
    public function confirm(Request $request, EmailChange $emailChange): RedirectResponse
    {
        $user = $request->user();

        abort_if($emailChange->user_id !== $user->id, 403);

        if ($emailChange->isConfirmed()) {
            return to_route('customer.settings', ['tab' => 'account'])
                ->with('email-changed', 'Your email address has already been updated.');
        }

        abort_if($emailChange->isExpired(), 403);

        if (User::query()->where('email', $emailChange->new_email)->exists()) {
            return to_route('customer.settings', ['tab' => 'account'])
                ->with('email-change-failed', 'That email address is no longer available. Please request a new change.');
        }

        $oldEmail = $user->email;

        DB::transaction(function () use ($user, $emailChange, $oldEmail): void {
            $user->update([
                'email' => $emailChange->new_email,
                'email_verified_at' => now(),
            ]);

            $emailChange->update(['confirmed_at' => now()]);

            $this->updateTeamMemberships($user, $oldEmail, $emailChange->new_email);
        });

        SyncStripeCustomerDetailsJob::dispatch($user);

        Notification::route('mail', $oldEmail)->notify(new EmailChangeCompleted($emailChange));

        return to_route('customer.settings', ['tab' => 'account'])
            ->with('email-changed', 'Your email address has been updated. Remember to update your Composer credentials (auth.json and any CI secrets) to use the new address, or plugin installs will fail.');
    }

    /**
     * Keep team membership records in sync with the user's new email,
     * skipping any team that already has a record for the new address.
     */
    protected function updateTeamMemberships(User $user, string $oldEmail, string $newEmail): void
    {
        $memberships = TeamUser::query()
            ->where('user_id', $user->id)
            ->where('email', $oldEmail)
            ->get();

        foreach ($memberships as $membership) {
            $emailTaken = TeamUser::query()
                ->where('team_id', $membership->team_id)
                ->where('email', $newEmail)
                ->exists();

            if (! $emailTaken) {
                $membership->update(['email' => $newEmail]);
            }
        }
    }
}
