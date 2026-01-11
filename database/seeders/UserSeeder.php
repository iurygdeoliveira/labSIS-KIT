<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as PermissionModel;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $resources = ['media', 'users', 'authentication-log'];

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                PermissionModel::firstOrCreate([
                    'name' => $permission->for($resource),
                    'guard_name' => $guard,
                ]);
            }
        }

        RoleType::ensureGlobalRoles($guard);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@labsis.dev.br'],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => null, // Admin se auto-aprova
            ],
        );
        // Global (sem tenant): fixar team_id = 0 para atribuições globais
        $globalResolver = app(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([RoleType::ADMIN->value]);
        // Reset do resolver para evitar vazamento de contexto
        $globalResolver->setPermissionsTeamId(null);

        // Garante que a role Admin possua todas as permissões
        $adminRole = Role::where('name', RoleType::ADMIN->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
            // Garante que a role Admin possua todas as permissões
            $adminRole->syncPermissions(PermissionModel::all());
        }

        $sicrano = User::query()->firstOrCreate(
            ['email' => 'sicrano@labsis.dev.br'],
            [
                'name' => 'Sicrano',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );

        $beltrano = User::query()->firstOrCreate(
            ['email' => 'beltrano@labsis.dev.br'],
            [
                'name' => 'Beltrano',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
                'is_approved' => true,
                'approved_by' => $admin->id,
            ],
        );
        // Não atribui roles no escopo global. Roles de usuário serão atribuídas por tenant abaixo.

        // Tenants para demonstração
        $tenantA = Tenant::firstOrCreate(
            ['name' => 'Tenant A'],
            [
                'uuid' => (string) Str::uuid(),
                'is_active' => true,
            ],
        );

        $tenantB = Tenant::firstOrCreate(
            ['name' => 'Tenant B'],
            [
                'uuid' => (string) Str::uuid(),
                'is_active' => true,
            ],
        );

        // Vincula os usuários aos tenants
        $sicrano->tenants()->syncWithoutDetaching([
            $tenantA->id,
            $tenantB->id,
        ]);

        $beltrano->tenants()->syncWithoutDetaching([
            $tenantA->id,
            $tenantB->id,
        ]);

        // Define roles por tenant
        // Garantir existência das roles por tenant
        $roleOwnerA = RoleType::ensureOwnerRoleForTeam($tenantA->id, $guard);
        $roleUserA = Role::firstOrCreate(['name' => RoleType::USER->value, 'team_id' => $tenantA->id]);
        $roleOwnerB = RoleType::ensureOwnerRoleForTeam($tenantB->id, $guard);
        $roleUserB = Role::firstOrCreate(['name' => RoleType::USER->value, 'team_id' => $tenantB->id]);

        // Limpar cache antes de atribuir permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Atribuir permissões às roles por tenant
        $this->assignPermissionsToRolesByTenant($roleOwnerA, $tenantA->id, $guard);
        $this->assignPermissionsToRolesByTenant($roleOwnerB, $tenantB->id, $guard);

        // Atribuições explícitas com team_id na tabela model_has_roles
        // Tenant A
        $sicrano->assignRoleInTenant($roleOwnerA, $tenantA);
        $beltrano->assignRoleInTenant($roleUserA, $tenantA);

        // Tenant B
        $beltrano->assignRoleInTenant($roleOwnerB, $tenantB);
        $sicrano->assignRoleInTenant($roleUserB, $tenantB);

        // Limpa o cache de permissões para garantir que as alterações sejam aplicadas
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Atribui permissões às roles Owner e User para um tenant específico
     */
    private function assignPermissionsToRolesByTenant(Role $ownerRole, int $tenantId, string $guard): void
    {
        $resources = ['media', 'users', 'authentication-log'];

        // Configurar o contexto de team para o tenant
        $teamResolver = app(SpatieTeamResolver::class);
        $teamResolver->setPermissionsTeamId($tenantId);

        // Owner recebe todas as permissões
        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                $permissionName = $permission->for($resource);
                $permissionModel = PermissionModel::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => $guard,
                ]);

                if (! $ownerRole->hasPermissionTo($permissionName, $guard)) {
                    $ownerRole->givePermissionTo($permissionName);
                }
            }
        }

        // Reset do resolver para evitar vazamento de contexto
        $teamResolver->setPermissionsTeamId(null);
    }
}
