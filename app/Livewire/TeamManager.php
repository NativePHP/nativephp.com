<?php

namespace App\Livewire;

use App\Models\Team;
use Livewire\Component;

class TeamManager extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function render()
    {
        $this->team->refresh();
        $this->team->load('users');

        $activeMembers = $this->team->users->where('status', \App\Enums\TeamUserStatus::Active);
        $pendingInvitations = $this->team->users->where('status', \App\Enums\TeamUserStatus::Pending);

        return view('livewire.team-manager', [
            'activeMembers' => $activeMembers,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }
}
