<?php

namespace App\Jobs;

use App\Models\User;
use App\Support\DiscordApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemoveDiscordMaxRoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        if (! $this->user->discord_id) {
            Log::info('Skipping Discord role removal - user has no Discord connected', [
                'user_id' => $this->user->id,
            ]);

            return;
        }

        $discord = DiscordApi::make();

        $success = $discord->removeMaxRole($this->user->discord_id);

        if ($success) {
            $this->user->update(['discord_role_granted_at' => null]);

            Log::info('Discord Max role removed successfully', [
                'user_id' => $this->user->id,
                'discord_id' => $this->user->discord_id,
            ]);
        } else {
            Log::warning('Failed to remove Discord Max role (user may not have role)', [
                'user_id' => $this->user->id,
                'discord_id' => $this->user->discord_id,
            ]);
        }
    }
}
