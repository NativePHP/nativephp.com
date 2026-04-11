<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GitHubAppStatus extends Component
{
    public function render(): \Illuminate\View\View
    {
        $user = Auth::user();
        $installations = $user->githubInstallations()->orderBy('account_login')->get();
        $plugins = $user->plugins()->get();

        // Check which plugin repos are covered by installations
        $pluginCoverage = [];
        foreach ($plugins as $plugin) {
            $repo = $plugin->getRepositoryOwnerAndName();

            if (! $repo) {
                continue;
            }

            $covered = $installations->contains(
                fn ($installation) => $installation->hasAccessToRepo($repo['owner'], $repo['repo'])
            );

            $pluginCoverage[] = [
                'plugin' => $plugin,
                'owner' => $repo['owner'],
                'repo' => $repo['repo'],
                'covered' => $covered,
            ];
        }

        return view('livewire.git-hub-app-status', [
            'installations' => $installations,
            'pluginCoverage' => $pluginCoverage,
            'slug' => config('services.github_app.slug'),
        ]);
    }
}
