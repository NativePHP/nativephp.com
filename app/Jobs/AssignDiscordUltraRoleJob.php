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

class AssignDiscordUltraRoleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(public User $user) {}

    public function handle(): void
    {
        if (! $this->user->discord_id) {
            Log::info('Skipping Discord role assignment - user has no Discord connected', [
                'user_id' => $this->user->id,
            ]);

            return;
        }

        if (! $this->user->hasMaxAccess() && ! $this->user->hasUltraAccess()) {
            Log::info('Skipping Discord role assignment - user has no Max or Ultra access', [
                'user_id' => $this->user->id,
            ]);

            return;
        }

        $discord = DiscordApi::make();

        if (! $discord->isGuildMember($this->user->discord_id)) {
            Log::info('Skipping Discord role assignment - user is not in guild', [
                'user_id' => $this->user->id,
                'discord_id' => $this->user->discord_id,
            ]);

            return;
        }

        $success = $discord->assignUltraRole($this->user->discord_id);

        if ($success) {
            $this->user->update(['discord_role_granted_at' => now()]);

            Log::info('Discord Ultra role assigned successfully', [
                'user_id' => $this->user->id,
                'discord_id' => $this->user->discord_id,
            ]);
        } else {
            Log::error('Failed to assign Discord Ultra role', [
                'user_id' => $this->user->id,
                'discord_id' => $this->user->discord_id,
            ]);
        }
    }
}
