<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\DiscordApi;
use Illuminate\Console\Command;

class RemoveExpiredDiscordRoles extends Command
{
    protected $signature = 'discord:remove-expired-roles';

    protected $description = 'Remove Discord Max role for users whose Max licenses have expired';

    public function handle(): int
    {
        $discord = DiscordApi::make();
        $removed = 0;

        $users = User::query()
            ->whereNotNull('discord_role_granted_at')
            ->whereNotNull('discord_id')
            ->get();

        foreach ($users as $user) {
            if (! $user->hasMaxAccess()) {
                $success = $discord->removeMaxRole($user->discord_id);

                if ($success) {
                    $user->update([
                        'discord_role_granted_at' => null,
                    ]);

                    $this->info("Removed Discord role for user: {$user->email} ({$user->discord_username})");
                    $removed++;
                } else {
                    $this->error("Failed to remove Discord role for user: {$user->email} ({$user->discord_username})");
                }
            }
        }

        $this->info("Total users with Discord role removed: {$removed}");

        return Command::SUCCESS;
    }
}
