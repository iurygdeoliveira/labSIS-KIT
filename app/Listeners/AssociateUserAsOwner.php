<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\RoleType;
use App\Events\TenantCreated;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssociateUserAsOwner implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TenantCreated $event): void
    {
        $user = $event->user;
        $tenant = $event->tenant;

        // Garantir que a role Owner existe para este tenant
        $ownerRole = RoleType::ensureOwnerRoleForTeam($tenant->id, 'web');

        // Atribuir permissões à role Owner
        $this->assignOwnerPermissions($ownerRole);

        // Associar usuário à role Owner do tenant
        $user->assignRole($ownerRole);
    }

    private function assignOwnerPermissions(Role $role): void
    {
        // Buscar todas as permissões disponíveis
        $permissions = \Spatie\Permission\Models\Permission::all();

        // Atribuir todas as permissões à role Owner
        $role->syncPermissions($permissions);
    }
}
