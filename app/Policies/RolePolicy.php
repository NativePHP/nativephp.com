<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

/**
 * Only super admins manage roles. Letting anyone else edit roles would let
 * them grant themselves any permission. The super admin role itself is
 * fixed: its access comes from the gate, not from its permission list.
 */
class RolePolicy
{
    public function viewAny(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    public function view(User $authUser, Role $role): bool
    {
        return $authUser->isAdmin();
    }

    public function create(User $authUser): bool
    {
        return $authUser->isAdmin();
    }

    public function update(User $authUser, Role $role): bool
    {
        return $authUser->isAdmin() && ! $this->isSuperAdminRole($role);
    }

    public function delete(User $authUser, Role $role): bool
    {
        return $authUser->isAdmin() && ! $this->isSuperAdminRole($role);
    }

    public function deleteAny(User $authUser): bool
    {
        return false;
    }

    protected function isSuperAdminRole(Role $role): bool
    {
        return $role->name === config('filament-shield.super_admin.name');
    }
}
