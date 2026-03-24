<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $user = Auth::user();
        $team = $user->ownedTeam;
        $membership = $user->activeTeamMembership();

        return view('customer.team.index', compact('team', 'membership'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->hasActiveUltraSubscription()) {
            return back()->with('error', 'You need an active Ultra subscription to create a team.');
        }

        if ($user->ownedTeam) {
            return back()->with('error', 'You already have a team.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user->ownedTeam()->create([
            'name' => $request->name,
        ]);

        return to_route('customer.team.index')
            ->with('success', 'Team created successfully!');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $team = $user->ownedTeam;

        if (! $team) {
            return back()->with('error', 'You do not own a team.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $team->update(['name' => $request->name]);

        return back()->with('success', 'Team name updated.');
    }
}
