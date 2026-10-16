<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use Laravel\Cashier\SubscriptionItem;

class SubscriptionItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SubscriptionItem');
    }

    public function view(AuthUser $authUser, SubscriptionItem $subscriptionItem): bool
    {
        return $authUser->can('View:SubscriptionItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SubscriptionItem');
    }

    public function update(AuthUser $authUser, SubscriptionItem $subscriptionItem): bool
    {
        return $authUser->can('Update:SubscriptionItem');
    }

    public function delete(AuthUser $authUser, SubscriptionItem $subscriptionItem): bool
    {
        return $authUser->can('Delete:SubscriptionItem');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SubscriptionItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SubscriptionItem');
    }
}
