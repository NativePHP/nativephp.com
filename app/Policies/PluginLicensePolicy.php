<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PluginLicense;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PluginLicensePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PluginLicense');
    }

    public function view(AuthUser $authUser, PluginLicense $pluginLicense): bool
    {
        return $authUser->can('View:PluginLicense');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PluginLicense');
    }

    public function update(AuthUser $authUser, PluginLicense $pluginLicense): bool
    {
        return $authUser->can('Update:PluginLicense');
    }

    public function delete(AuthUser $authUser, PluginLicense $pluginLicense): bool
    {
        return $authUser->can('Delete:PluginLicense');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PluginLicense');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PluginLicense');
    }

    public function refund(AuthUser $authUser, PluginLicense $pluginLicense): bool
    {
        return $authUser->can('Refund:PluginLicense');
    }
}
