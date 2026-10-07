<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\License;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LicensePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:License');
    }

    public function view(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('View:License');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:License');
    }

    public function update(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('Update:License');
    }

    public function delete(AuthUser $authUser, License $license): bool
    {
        return $authUser->can('Delete:License');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:License');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:License');
    }
}
