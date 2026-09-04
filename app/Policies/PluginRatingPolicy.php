<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Plugin;
use App\Models\PluginRating;
use App\Models\User;

final class PluginRatingPolicy
{
    public function create(User $user, Plugin $plugin): bool
    {
        return $user->canRatePlugin($plugin);
    }

    public function update(User $user, PluginRating $pluginRating): bool
    {
        return $user->id === $pluginRating->user_id;
    }

    public function delete(User $user, PluginRating $pluginRating): bool
    {
        return $user->id === $pluginRating->user_id || $user->isAdmin();
    }
}
