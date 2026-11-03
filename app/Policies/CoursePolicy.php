<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Course;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CoursePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Course');
    }

    public function view(AuthUser $authUser, Course $course): bool
    {
        return $authUser->can('View:Course');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Course');
    }

    public function update(AuthUser $authUser, Course $course): bool
    {
        return $authUser->can('Update:Course');
    }

    public function delete(AuthUser $authUser, Course $course): bool
    {
        return $authUser->can('Delete:Course');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Course');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Course');
    }
}
