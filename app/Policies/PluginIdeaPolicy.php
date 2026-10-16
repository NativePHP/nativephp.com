<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PluginIdea;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PluginIdeaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PluginIdea');
    }

    public function view(AuthUser $authUser, PluginIdea $pluginIdea): bool
    {
        return $authUser->can('View:PluginIdea');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PluginIdea');
    }

    public function update(AuthUser $authUser, PluginIdea $pluginIdea): bool
    {
        return $authUser->can('Update:PluginIdea');
    }

    public function delete(AuthUser $authUser, PluginIdea $pluginIdea): bool
    {
        return $authUser->can('Delete:PluginIdea');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PluginIdea');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PluginIdea');
    }
}
