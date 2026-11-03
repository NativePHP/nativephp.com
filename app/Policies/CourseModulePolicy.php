<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CourseModule;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CourseModulePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseModule');
    }

    public function view(AuthUser $authUser, CourseModule $courseModule): bool
    {
        return $authUser->can('View:CourseModule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseModule');
    }

    public function update(AuthUser $authUser, CourseModule $courseModule): bool
    {
        return $authUser->can('Update:CourseModule');
    }

    public function delete(AuthUser $authUser, CourseModule $courseModule): bool
    {
        return $authUser->can('Delete:CourseModule');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CourseModule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CourseModule');
    }
}
