<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SuspendTeamJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public int $userId) {}

    public function handle(): void
    {
        $user = User::find($this->userId);

        if (! $user) {
            return;
        }

        $team = $user->ownedTeam;

        if (! $team) {
            return;
        }

        $team->suspend();

        Log::info('Team suspended due to subscription change', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);

        // Revoke access for all active members
        $team->activeUsers()
            ->whereNotNull('user_id')
            ->each(function ($member): void {
                dispatch(new RevokeTeamUserAccessJob($member->user_id));
            });
    }
}
