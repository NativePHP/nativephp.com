<?php

namespace App\Livewire;

use App\Support\GitHubOAuth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class GitHubAccessBanner extends Component
{
    public bool $inline = false;

    public ?string $collaboratorStatus = null;

    public function mount(bool $inline = false): void
    {
        $this->inline = $inline;
        $this->checkCollaboratorStatus();
    }

    public function checkCollaboratorStatus(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->github_username) {
            $this->collaboratorStatus = null;

            return;
        }

        // Cache the status for 5 minutes to avoid excessive API calls
        $cacheKey = "github_collab_status_{$user->id}";

        $this->collaboratorStatus = Cache::remember($cacheKey, 300, function () use ($user) {
            $github = GitHubOAuth::make();

            return $github->checkCollaboratorStatus($user->github_username);
        });

        // If they have active access but we haven't recorded it, update our record
        if ($this->collaboratorStatus === 'active' && ! $user->mobile_repo_access_granted_at) {
            $user->update(['mobile_repo_access_granted_at' => now()]);
        }
    }

    public function refreshStatus(): void
    {
        $user = auth()->user();

        if ($user) {
            Cache::forget("github_collab_status_{$user->id}");
        }

        $this->checkCollaboratorStatus();
    }

    public function render()
    {
        return view('livewire.git-hub-access-banner');
    }
}
