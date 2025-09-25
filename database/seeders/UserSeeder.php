<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            ['email' => 'admin@labsis.dev.br'],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
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
            ],
        );

        $beltrano = User::query()->firstOrCreate(
            ['email' => 'beltrano@labsis.dev.br'],
            [
                'name' => 'Beltrano',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
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
        $roleUserA = RoleType::ensureUserRoleForTeam($tenantA->id, $guard);
        $roleOwnerB = RoleType::ensureOwnerRoleForTeam($tenantB->id, $guard);
        $roleUserB = RoleType::ensureUserRoleForTeam($tenantB->id, $guard);

        // Atribuições explícitas com team_id na tabela model_has_roles
        DB::table('model_has_roles')->insert([
            // Tenant A
            [
                'role_id' => $roleOwnerA->id,
                'model_type' => User::class,
                'model_id' => $sicrano->id,
                'team_id' => $tenantA->id,
            ],
            [
                'role_id' => $roleUserA->id,
                'model_type' => User::class,
                'model_id' => $beltrano->id,
                'team_id' => $tenantA->id,
            ],
            // Tenant B
            [
                'role_id' => $roleOwnerB->id,
                'model_type' => User::class,
                'model_id' => $beltrano->id,
                'team_id' => $tenantB->id,
            ],
            [
                'role_id' => $roleUserB->id,
                'model_type' => User::class,
                'model_id' => $sicrano->id,
                'team_id' => $tenantB->id,
            ],
        ]);

        // Limpa o cache de permissões para garantir que as alterações sejam aplicadas
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
