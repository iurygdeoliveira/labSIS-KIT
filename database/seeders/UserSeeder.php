<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
        $user->syncRoles([RoleType::USER->value]);
    }
}
