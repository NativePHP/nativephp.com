<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PluginPayout;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PluginPayoutPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PluginPayout');
    }

    public function view(AuthUser $authUser, PluginPayout $pluginPayout): bool
    {
        return $authUser->can('View:PluginPayout');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PluginPayout');
    }

    public function update(AuthUser $authUser, PluginPayout $pluginPayout): bool
    {
        return $authUser->can('Update:PluginPayout');
    }

    public function delete(AuthUser $authUser, PluginPayout $pluginPayout): bool
    {
        return $authUser->can('Delete:PluginPayout');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PluginPayout');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PluginPayout');
    }
}
