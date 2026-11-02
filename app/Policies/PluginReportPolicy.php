<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Plugin;
use App\Models\PluginReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

final class PluginReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PluginReport');
    }

    public function view(User $user, PluginReport $pluginReport): Response
    {
        return $user->can('View:PluginReport')
            ? Response::allow()
            : Response::denyAsNotFound('Report not found.');
    }

    public function update(User $user, PluginReport $pluginReport): bool
    {
        return $user->can('Update:PluginReport');
    }

    public function create(User $user, Plugin $plugin): bool
    {
        return $user->canReportPlugin($plugin);
    }
}
