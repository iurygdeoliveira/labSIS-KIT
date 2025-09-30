<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Facades\Filament;

class TenantPolicy
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
        return $user->can(Permission::VIEW->for('tenants'));
    }

    public function view(User $user, Tenant $record): bool
    {
        return $user->can(Permission::VIEW->for('tenants'));
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE->for('tenants'));
    }

    public function update(User $user, Tenant $record): bool
    {
        return $user->can(Permission::UPDATE->for('tenants'));
    }

    public function delete(User $user, Tenant $record): bool
    {
        return $user->can(Permission::DELETE->for('tenants'));
    }

    public function deleteAny(User $user): bool
    {
        return $user->can(Permission::DELETE->for('tenants'));
    }
}
