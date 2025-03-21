<?php

namespace App\Providers;

use App\Support\GitHub;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerSharedViewVariables();
    }

    private function registerSharedViewVariables(): void
    {
        View::share('electronGitHubVersion', GitHub::electron()->latestVersion());
        View::share('discordLink', 'https://discord.gg/X62tWNStZK');
        View::share('bskyLink', 'https://bsky.app/profile/nativephp.bsky.social');
        View::share('openCollectiveLink', 'https://opencollective.com/nativephp');
        View::share('githubLink', 'https://github.com/NativePHP');
    }
}
