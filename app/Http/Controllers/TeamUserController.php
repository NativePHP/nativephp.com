<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TeamUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($team->user_id !== $user->id || ! $user->hasUltraAccess()) {
            abort(403);
        }

        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (! $team->hasAvailableSeats()) {
            return back()->withErrors([
                'email' => 'All seats are occupied. Add more seats in your team settings to invite more members.',
            ]);
        }

        // Check for existing invitation or membership
        if ($team->members()->where('email', $request->email)->exists()) {
            return back()->withErrors([
                'email' => 'This email already has an invitation or is already a member.',
            ]);
        }

        TeamUser::create([
            'team_id' => $team->id,
            'email' => $request->email,
            'role' => 'member',
            'status' => 'pending',
            'invitation_token' => Str::random(64),
            'invited_at' => now(),
        ]);

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function destroy(Team $team, TeamUser $teamUser): RedirectResponse
    {
        $user = Auth::user();

        if ($team->user_id !== $user->id || ! $user->hasUltraAccess()) {
            abort(403);
        }

        if ($teamUser->team_id !== $team->id) {
            abort(404);
        }

        $teamUser->delete();

        return back()->with('success', 'Member removed successfully.');
    }
}
