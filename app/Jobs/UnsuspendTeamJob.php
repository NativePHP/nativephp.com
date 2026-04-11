<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UnsuspendTeamJob implements ShouldQueue
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

        if (! $team || ! $team->is_suspended) {
            return;
        }

        if (! $user->hasMaxAccess()) {
            return;
        }

        $team->unsuspend();

        Log::info('Team unsuspended due to subscription reactivation', [
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }
}
