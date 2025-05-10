<?php

namespace App\Providers;

use App\Support\GitHub;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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
}
