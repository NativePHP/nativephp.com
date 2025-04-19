<?php

namespace App\Providers;

use App\Support\GitHub;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret'));
        });
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
        View::share('electronGitHubVersion', app()->environment('production')
            ? GitHub::electron()->latestVersion()
            : 'dev'
        );
        View::share('discordLink', 'https://discord.gg/X62tWNStZK');
        View::share('bskyLink', 'https://bsky.app/profile/nativephp.bsky.social');
        View::share('openCollectiveLink', 'https://opencollective.com/nativephp');
        View::share('githubLink', 'https://github.com/NativePHP');
    }
}
