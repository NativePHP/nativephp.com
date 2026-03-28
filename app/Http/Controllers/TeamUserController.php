<?php

namespace App\Http\Controllers;

use App\Enums\TeamUserStatus;
use App\Http\Requests\InviteTeamUserRequest;
use App\Jobs\RevokeTeamUserAccessJob;
use App\Models\TeamUser;
use App\Notifications\TeamInvitation;
use App\Notifications\TeamUserRemoved;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

class TeamUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('accept');
    }

    public function invite(InviteTeamUserRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $team = $user->ownedTeam;

        if (! $team) {
            return back()->with('error', 'You do not have a team.');
        }

        if ($team->is_suspended) {
            return back()->with('error', 'Your team is currently suspended.');
        }

        // Rate limit: 5 invites per minute per team
        $rateLimitKey = "team-invite:{$team->id}";
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            return back()->with('error', 'Too many invitations sent. Please wait a moment.');
        }
        RateLimiter::hit($rateLimitKey, 60);

        $email = $request->validated()['email'];

        // Prevent owner from inviting themselves
        if (strtolower($email) === strtolower($user->email)) {
            return back()->with('error', 'You cannot invite yourself to your own team.');
        }

        // Check for duplicate (active or pending)
        $existingMember = $team->users()
            ->where('email', $email)
            ->whereIn('status', [TeamUserStatus::Pending, TeamUserStatus::Active])
            ->first();

        if ($existingMember) {
            return back()->with('error', 'This email has already been invited or is an active member.');
        }

        if ($team->isOverIncludedLimit()) {
            return back()
                ->with('error', 'You have no available seats. Add extra seats to invite more members.')
                ->with('show_add_seats', true);
        }

        // Reuse existing removed record if re-inviting a previously removed member
        $existingRemoved = $team->users()
            ->where('email', $email)
            ->where('status', TeamUserStatus::Removed)
            ->first();

        if ($existingRemoved) {
            $existingRemoved->update([
                'status' => TeamUserStatus::Pending,
                'user_id' => null,
                'invitation_token' => bin2hex(random_bytes(32)),
                'invited_at' => now(),
                'accepted_at' => null,
            ]);
            $member = $existingRemoved;
        } else {
            $member = $team->users()->create([
                'email' => $email,
                'invitation_token' => bin2hex(random_bytes(32)),
                'invited_at' => now(),
            ]);
        }

        Notification::route('mail', $email)
            ->notify(new TeamInvitation($member));

        return back()->with('success', "Invitation sent to {$email}.");
    }

    public function remove(TeamUser $teamUser): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->ownedTeam || $teamUser->team_id !== $user->ownedTeam->id) {
            return back()->with('error', 'You are not authorized to remove this member.');
        }

        $teamUser->remove();

        Notification::route('mail', $teamUser->email)
            ->notify(new TeamUserRemoved($teamUser));

        if ($teamUser->user_id) {
            dispatch(new RevokeTeamUserAccessJob($teamUser->user_id));
        }

        return back()->with('success', "{$teamUser->email} has been removed from the team.");
    }

    public function resend(TeamUser $teamUser): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->ownedTeam || $teamUser->team_id !== $user->ownedTeam->id) {
            return back()->with('error', 'You are not authorized to resend this invitation.');
        }

        if (! $teamUser->isPending()) {
            return back()->with('error', 'This invitation cannot be resent.');
        }

        // Rate limit: 1 resend per minute per member
        $rateLimitKey = "team-resend:{$teamUser->id}";
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return back()->with('error', 'Please wait before resending this invitation.');
        }
        RateLimiter::hit($rateLimitKey, 60);

        Notification::route('mail', $teamUser->email)
            ->notify(new TeamInvitation($teamUser));

        return back()->with('success', "Invitation resent to {$teamUser->email}.");
    }

    public function accept(string $token): RedirectResponse
    {
        $teamUser = TeamUser::where('invitation_token', $token)
            ->where('status', TeamUserStatus::Pending)
            ->first();

        if (! $teamUser) {
            return to_route('dashboard')
                ->with('error', 'This invitation is invalid or has already been used.');
        }

        $user = Auth::user();

        if ($user) {
            // Authenticated user
            if (strtolower($user->email) !== strtolower($teamUser->email)) {
                return to_route('dashboard')
                    ->with('error', 'This invitation was sent to a different email address.');
            }

            $teamUser->accept($user);

            return to_route('dashboard')
                ->with('success', "You've joined {$teamUser->team->name}!");
        }

        // Not authenticated — store token in session and redirect to login
        session(['pending_team_invitation_token' => $token]);

        return to_route('customer.login')
            ->with('message', 'Please log in or register to accept your team invitation.');
    }
}
