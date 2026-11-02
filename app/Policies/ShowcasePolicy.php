<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Showcase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ShowcasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Showcase');
    }

    public function view(AuthUser $authUser, Showcase $showcase): bool
    {
        return $authUser->can('View:Showcase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Showcase');
    }

    public function update(AuthUser $authUser, Showcase $showcase): bool
    {
        return $authUser->can('Update:Showcase');
    }

    public function delete(AuthUser $authUser, Showcase $showcase): bool
    {
        return $authUser->can('Delete:Showcase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Showcase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Showcase');
    }
}
