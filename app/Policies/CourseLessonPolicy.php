<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CourseLesson;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CourseLessonPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CourseLesson');
    }

    public function view(AuthUser $authUser, CourseLesson $courseLesson): bool
    {
        return $authUser->can('View:CourseLesson');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CourseLesson');
    }

    public function update(AuthUser $authUser, CourseLesson $courseLesson): bool
    {
        return $authUser->can('Update:CourseLesson');
    }

    public function delete(AuthUser $authUser, CourseLesson $courseLesson): bool
    {
        return $authUser->can('Delete:CourseLesson');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CourseLesson');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CourseLesson');
    }
}
