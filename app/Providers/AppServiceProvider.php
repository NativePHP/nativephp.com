<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerSharedViewVariables();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function registerSharedViewVariables()
    {
        View::share('discordLink', 'https://discord.gg/X62tWNStZK');
        View::share('bskyLink', 'https://bsky.app/profile/nativephp.bsky.social');
        View::share('openCollectiveLink', 'https://opencollective.com/nativephp');
        View::share('githubLink', 'https://github.com/NativePHP');
    }
}
