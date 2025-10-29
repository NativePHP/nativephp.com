<?php

namespace App\Providers;

use App\Features\ShowAuthButtons;
use App\Support\GitHub;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use Sentry\State\Scope;

use function Sentry\captureException;
use function Sentry\configureScope;

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

        $this->sendFailingJobsToSentry();

        $this->registerFeatureFlags();

        RateLimiter::for('anystack', function () {
            return Limit::perMinute(30);
        });
    }

    private function registerSharedViewVariables(): void
    {
        View::share('electronGitHubVersion', app()->environment('production')
            ? GitHub::electron()->latestVersion()
            : 'dev'
        );
        View::share('discordLink', 'https://discord.gg/nativephp');
        View::share('bskyLink', 'https://bsky.app/profile/nativephp.com');
        View::share('openCollectiveLink', 'https://opencollective.com/nativephp');
        View::share('githubLink', 'https://github.com/nativephp');
    }

    private function sendFailingJobsToSentry(): void
    {
        Queue::failing(static function (JobFailed $event) {
            if (app()->bound('sentry')) {
                configureScope(function (Scope $scope) use ($event): void {
                    $scope->setContext('job', [
                        'connection' => $event->connectionName,
                        'queue' => $event->job->getQueue(),
                        'name' => $event->job->resolveName(),
                        'payload' => $event->job->payload(),
                    ]);
                });

                captureException($event->exception);
            }
        });
    }

    private function registerFeatureFlags(): void
    {
        Feature::define(ShowAuthButtons::class);
    }
}
