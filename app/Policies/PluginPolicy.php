<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Plugin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PluginPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Plugin');
    }

    public function view(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('View:Plugin');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Plugin');
    }

    public function update(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('Update:Plugin');
    }

    public function delete(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('Delete:Plugin');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Plugin');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Plugin');
    }

    public function approve(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('Approve:Plugin');
    }

    public function reject(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('Reject:Plugin');
    }

    public function messageDeveloper(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('MessageDeveloper:Plugin');
    }

    public function runReviewChecks(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('RunReviewChecks:Plugin');
    }

    public function convertToPaid(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('ConvertToPaid:Plugin');
    }

    public function grantToUser(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('GrantToUser:Plugin');
    }

    public function syncToSatis(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('SyncToSatis:Plugin');
    }

    public function resync(AuthUser $authUser, Plugin $plugin): bool
    {
        return $authUser->can('Resync:Plugin');
    }
}
