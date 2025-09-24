<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');
        $resources = ['media', 'users'];

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
            ['email' => 'fulano@labsis.dev.br'],
            [
                'name' => 'fulano',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
            ],
        );
        // Global (sem tenant): fixar team_id = 0 para atribuições globais
        $globalResolver = app(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([RoleType::ADMIN->value]);

        // Garante que a role Admin possua todas as permissões
        $adminRole = Role::where('name', RoleType::ADMIN->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
            // Garante que a role Admin possua todas as permissões
            $adminRole->syncPermissions(PermissionModel::all());
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'sicrano@labsis.dev.br'],
            [
                'name' => 'sicrano',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
            ],
        );
        // Não atribui role "User" no escopo global. Roles de usuário serão atribuídas por tenant abaixo.

        // Tenants para o 'sicrano' (sem slug)
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

        // Vincula o usuário aos dois tenants (sem atributos de pivot)
        $user->tenants()->syncWithoutDetaching([
            $tenantA->id,
            $tenantB->id,
        ]);

        // Define roles por tenant usando o team resolver
        $resolver = app(SpatieTeamResolver::class);

        // Team: tenant A
        $resolver->setPermissionsTeamId($tenantA->id);
        $roleUserA = RoleType::ensureUserRoleForTeam($tenantA->id, $guard);
        $user->assignRole($roleUserA);

        // Role User do tenant A criada sem permissões - será atribuída manualmente pelo admin

        // Team: tenant B
        $resolver->setPermissionsTeamId($tenantB->id);
        $roleUserB = RoleType::ensureUserRoleForTeam($tenantB->id, $guard);
        $user->assignRole($roleUserB);

        // Role User do tenant B criada sem permissões - será atribuída manualmente pelo admin

        // Limpa override do team id para não vazar para outros seeders
        $resolver->setPermissionsTeamId(null);
    }
}
