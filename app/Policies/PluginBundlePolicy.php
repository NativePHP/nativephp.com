<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PluginBundle;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PluginBundlePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PluginBundle');
    }

    public function view(AuthUser $authUser, PluginBundle $pluginBundle): bool
    {
        return $authUser->can('View:PluginBundle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PluginBundle');
    }

    public function update(AuthUser $authUser, PluginBundle $pluginBundle): bool
    {
        return $authUser->can('Update:PluginBundle');
    }

    public function delete(AuthUser $authUser, PluginBundle $pluginBundle): bool
    {
        return $authUser->can('Delete:PluginBundle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PluginBundle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PluginBundle');
    }
}
