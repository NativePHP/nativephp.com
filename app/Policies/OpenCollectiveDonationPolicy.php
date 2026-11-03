<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OpenCollectiveDonation;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class OpenCollectiveDonationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OpenCollectiveDonation');
    }

    public function view(AuthUser $authUser, OpenCollectiveDonation $openCollectiveDonation): bool
    {
        return $authUser->can('View:OpenCollectiveDonation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OpenCollectiveDonation');
    }

    public function update(AuthUser $authUser, OpenCollectiveDonation $openCollectiveDonation): bool
    {
        return $authUser->can('Update:OpenCollectiveDonation');
    }

    public function delete(AuthUser $authUser, OpenCollectiveDonation $openCollectiveDonation): bool
    {
        return $authUser->can('Delete:OpenCollectiveDonation');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OpenCollectiveDonation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OpenCollectiveDonation');
    }
}
