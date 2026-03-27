<?php

namespace App\Http\Controllers;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Enums\TeamUserStatus;
use App\Models\Plugin;
use App\Models\Team;
use App\Models\TeamUser;
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

    public function show(Team $team): View|RedirectResponse
    {
        $user = Auth::user();

        // Team owners should use the manage page
        if ($user->ownedTeam && $user->ownedTeam->id === $team->id) {
            return to_route('customer.team.index');
        }

        // Verify user is an active member of this team
        $membership = TeamUser::query()
            ->where('team_id', $team->id)
            ->where('user_id', $user->id)
            ->where('status', TeamUserStatus::Active)
            ->first();

        if (! $membership) {
            abort(403);
        }

        // All plugins accessible through this team (official + owner's purchased), de-duplicated
        $officialPlugins = Plugin::query()
            ->where('is_official', true)
            ->where('is_active', true)
            ->where('status', PluginStatus::Approved)
            ->where('type', PluginType::Paid)
            ->get();

        $ownerPlugins = $team->owner
            ->pluginLicenses()
            ->active()
            ->with('plugin')
            ->get()
            ->pluck('plugin')
            ->filter();

        $plugins = $officialPlugins->merge($ownerPlugins)->unique('id')->sortBy('name')->values();

        return view('customer.team.show', compact('team', 'membership', 'plugins'));
    }
}
