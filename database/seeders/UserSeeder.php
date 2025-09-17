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

        Role::firstOrCreate(['name' => RoleType::ADMIN->value, 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => RoleType::USER->value, 'guard_name' => $guard]);

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
        $globalResolver->setPermissionsTeamId(0);
        $user->syncRoles([RoleType::USER->value]);

        // Tenants para o 'sicrano'
        $tenantA = Tenant::firstOrCreate(
            ['slug' => 'tenant-a'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Tenant A',
                'is_active' => true,
            ],
        );

        $tenantB = Tenant::firstOrCreate(
            ['slug' => 'tenant-b'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Tenant B',
                'is_active' => true,
            ],
        );

        // Vincula o usuário aos dois tenants
        $user->tenants()->syncWithoutDetaching([
            $tenantA->id => ['is_owner' => true],
            $tenantB->id => ['is_owner' => false],
        ]);

        // Define roles por tenant usando o team resolver
        $resolver = app(SpatieTeamResolver::class);

        // Team: tenant-a
        $resolver->setPermissionsTeamId($tenantA->id);
        $roleUserA = Role::firstOrCreate([
            'team_id' => $tenantA->id,
            'name' => 'user',
            'guard_name' => $guard,
        ]);
        $user->assignRole($roleUserA);

        // Team: tenant-b
        $resolver->setPermissionsTeamId($tenantB->id);
        $roleManagerB = Role::firstOrCreate([
            'team_id' => $tenantB->id,
            'name' => 'manager',
            'guard_name' => $guard,
        ]);
        $user->assignRole($roleManagerB);

        // Limpa override do team id para não vazar para outros seeders
        $resolver->setPermissionsTeamId(null);
    }
}
