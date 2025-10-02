<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;

class UserPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Tenant && $user->isOwnerOfTenant($currentTenant)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    public function view(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::VIEW);
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, Permission::CREATE);
    }

    public function update(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::UPDATE);
    }

    public function delete(User $user, User $record): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    public function deleteAny(User $user): bool
    {
        return $this->hasPermission($user, Permission::DELETE);
    }

    private function hasPermission(User $user, Permission $permission): bool
    {
        return $user->can($permission->for('users'));
    }
}
