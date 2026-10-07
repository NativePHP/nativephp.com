<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Article;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ArticlePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Article');
    }

    public function view(AuthUser $authUser, Article $article): bool
    {
        return $authUser->can('View:Article');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Article');
    }

    public function update(AuthUser $authUser, Article $article): bool
    {
        return $authUser->can('Update:Article');
    }

    public function delete(AuthUser $authUser, Article $article): bool
    {
        return $authUser->can('Delete:Article');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Article');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Article');
    }
}
