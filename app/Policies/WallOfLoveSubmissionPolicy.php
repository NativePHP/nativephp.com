<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\WallOfLoveSubmission;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class WallOfLoveSubmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:WallOfLoveSubmission');
    }

    public function view(AuthUser $authUser, WallOfLoveSubmission $wallOfLoveSubmission): bool
    {
        return $authUser->can('View:WallOfLoveSubmission');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:WallOfLoveSubmission');
    }

    public function update(AuthUser $authUser, WallOfLoveSubmission $wallOfLoveSubmission): bool
    {
        return $authUser->can('Update:WallOfLoveSubmission');
    }

    public function delete(AuthUser $authUser, WallOfLoveSubmission $wallOfLoveSubmission): bool
    {
        return $authUser->can('Delete:WallOfLoveSubmission');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:WallOfLoveSubmission');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:WallOfLoveSubmission');
    }
}
