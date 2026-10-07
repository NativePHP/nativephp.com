<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\SubscriptionItemPolicy;
use App\Policies\SubscriptionPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Cashier\Subscription;
use Laravel\Cashier\SubscriptionItem;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Subscription::class => SubscriptionPolicy::class,
        SubscriptionItem::class => SubscriptionItemPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability, array $arguments): ?bool {
            if ($user->isAdmin()) {
                return $this->isPanelPermission($ability) ? true : null;
            }

            return $this->lacksPolicyMethod($ability, $arguments[0] ?? null) ? false : null;
        });
    }

    /**
     * Shield permissions look like "ViewAny:Plugin". Super admins hold all of them,
     * while ordinary policy abilities (e.g. a customer's own support ticket) still
     * go through their policies untouched.
     */
    protected function isPanelPermission(string $ability): bool
    {
        return str_contains($ability, config('filament-shield.permissions.separator'));
    }

    /**
     * Filament allows anything a model has no policy for. Deny those to everyone
     * but super admins, so a new admin section stays locked until it has a policy.
     */
    protected function lacksPolicyMethod(string $ability, mixed $model): bool
    {
        $isModel = $model instanceof Model || (is_string($model) && is_subclass_of($model, Model::class));

        if (! $isModel || Gate::has($ability)) {
            return false;
        }

        $policy = Gate::getPolicyFor($model);

        return $policy === null || ! method_exists($policy, $ability);
    }
}
