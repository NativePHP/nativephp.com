<?php

namespace App\Livewire;

use App\Support\DiscordApi;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DiscordAccessBanner extends Component
{
    public bool $inline = false;

    public bool $hasMaxRole = false;

    public bool $isGuildMember = false;

    public function mount(bool $inline = false): void
    {
        $this->inline = $inline;
        $this->checkRoleStatus();
    }

    public function checkRoleStatus(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->discord_id) {
            $this->hasMaxRole = false;
            $this->isGuildMember = false;

            return;
        }

        $cacheKey = "discord_role_status_{$user->id}";

        $status = Cache::remember($cacheKey, 300, function () use ($user) {
            $discord = DiscordApi::make();

            return [
                'isGuildMember' => $discord->isGuildMember($user->discord_id),
                'hasMaxRole' => $discord->hasMaxRole($user->discord_id),
            ];
        });

        $this->isGuildMember = $status['isGuildMember'];
        $this->hasMaxRole = $status['hasMaxRole'];

        if ($this->hasMaxRole && ! $user->discord_role_granted_at) {
            $user->update(['discord_role_granted_at' => now()]);
        }
    }

    public function refreshStatus(): void
    {
        $user = auth()->user();

        if ($user) {
            Cache::forget("discord_role_status_{$user->id}");
        }

        $this->checkRoleStatus();
    }

    public function requestMaxRole(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->discord_id) {
            session()->flash('error', 'Please connect your Discord account first.');

            return;
        }

        if (! $user->hasMaxAccess()) {
            session()->flash('error', 'You need an active Max license to receive the Max role.');

            return;
        }

        $discord = DiscordApi::make();

        if (! $discord->isGuildMember($user->discord_id)) {
            session()->flash('error', 'Please join the NativePHP Discord server first.');

            return;
        }

        $success = $discord->assignMaxRole($user->discord_id);

        if ($success) {
            $user->update(['discord_role_granted_at' => now()]);
            Cache::forget("discord_role_status_{$user->id}");
            $this->checkRoleStatus();
            session()->flash('success', 'Max role assigned successfully!');
        } else {
            session()->flash('error', 'Failed to assign Max role. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.discord-access-banner');
    }
}
