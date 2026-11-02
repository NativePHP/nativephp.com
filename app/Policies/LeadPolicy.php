<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Lead;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Lead');
    }

    public function view(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('View:Lead');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Lead');
    }

    public function update(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('Update:Lead');
    }

    public function delete(AuthUser $authUser, Lead $lead): bool
    {
        return $authUser->can('Delete:Lead');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Lead');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Lead');
    }
}
