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

        foreach (PermissionEnum::cases() as $permission) {
            PermissionModel::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => $guard,
            ]);
        }

        Role::firstOrCreate(['name' => RoleType::ADMIN->value, 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => RoleType::USER->value, 'guard_name' => $guard]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@labsis.dev.br'],
            [
                'name' => 'Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
            ],
        );
        $admin->syncRoles([RoleType::ADMIN->value]);

        $user = User::query()->firstOrCreate(
            ['email' => 'user@labsis.dev.br'],
            [
                'name' => 'Usuário Padrão',
                'email_verified_at' => now(),
                'password' => Hash::make('mudar123'),
            ],
        );
        $user->syncRoles([RoleType::USER->value]);
    }
}
