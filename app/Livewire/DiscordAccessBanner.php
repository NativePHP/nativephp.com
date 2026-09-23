<?php

namespace App\Livewire;

use App\Support\DiscordApi;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DiscordAccessBanner extends Component
{
    public bool $inline = false;

    public bool $hasUltraRole = false;

    public bool $hasEarlyAdopterRole = false;

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
            $this->hasUltraRole = false;
            $this->hasEarlyAdopterRole = false;
            $this->isGuildMember = false;

            return;
        }

        $cacheKey = "discord_role_status_{$user->id}";

        $status = Cache::remember($cacheKey, 300, function () use ($user) {
            $discord = DiscordApi::make();

            return [
                'isGuildMember' => $discord->isGuildMember($user->discord_id),
                'hasUltraRole' => $discord->hasUltraRole($user->discord_id),
                'hasEarlyAdopterRole' => $discord->hasEarlyAdopterRole($user->discord_id),
            ];
        });

        $this->isGuildMember = $status['isGuildMember'];
        $this->hasUltraRole = $status['hasUltraRole'];
        $this->hasEarlyAdopterRole = $status['hasEarlyAdopterRole'];

        if ($this->hasUltraRole && ! $user->discord_role_granted_at) {
            $user->update(['discord_role_granted_at' => now()]);
        }

        if ($this->hasEarlyAdopterRole && ! $user->discord_early_adopter_role_granted_at) {
            $user->update(['discord_early_adopter_role_granted_at' => now()]);
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

    public function requestUltraRole(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->discord_id) {
            session()->flash('error', 'Please connect your Discord account first.');

            return;
        }

        if (! $user->hasMaxAccess() && ! $user->hasUltraAccess()) {
            session()->flash('error', 'You need an active Max license or Ultra subscription to receive the Ultra role.');

            return;
        }

        $discord = DiscordApi::make();

        if (! $discord->isGuildMember($user->discord_id)) {
            session()->flash('error', 'Please join the NativePHP Discord server first.');

            return;
        }

        $success = $discord->assignUltraRole($user->discord_id);

        if ($success) {
            $user->update(['discord_role_granted_at' => now()]);
            Cache::forget("discord_role_status_{$user->id}");
            $this->checkRoleStatus();
            session()->flash('success', 'Ultra role assigned successfully!');
        } else {
            session()->flash('error', 'Failed to assign Ultra role. Please try again later.');
        }
    }

    public function requestEarlyAdopterRole(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->discord_id) {
            session()->flash('error', 'Please connect your Discord account first.');

            return;
        }

        if (! $user->isEapCustomer()) {
            session()->flash('error', 'The Early Adopter role is for early access program customers.');

            return;
        }

        $discord = DiscordApi::make();

        if (! $discord->isGuildMember($user->discord_id)) {
            session()->flash('error', 'Please join the NativePHP Discord server first.');

            return;
        }

        $success = $discord->assignEarlyAdopterRole($user->discord_id);

        if ($success) {
            $user->update(['discord_early_adopter_role_granted_at' => now()]);
            Cache::forget("discord_role_status_{$user->id}");
            $this->checkRoleStatus();
            session()->flash('success', 'Early Adopter role assigned successfully!');
        } else {
            session()->flash('error', 'Failed to assign Early Adopter role. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.discord-access-banner');
    }
}
