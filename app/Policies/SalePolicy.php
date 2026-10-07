<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Sale;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SalePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sale');
    }

    public function view(AuthUser $authUser, Sale $sale): bool
    {
        return $authUser->can('View:Sale');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sale');
    }

    public function update(AuthUser $authUser, Sale $sale): bool
    {
        return $authUser->can('Update:Sale');
    }

    public function delete(AuthUser $authUser, Sale $sale): bool
    {
        return $authUser->can('Delete:Sale');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Sale');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sale');
    }
}
