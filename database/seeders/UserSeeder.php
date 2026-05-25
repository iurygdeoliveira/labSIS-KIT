<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AppTeamRole;
use App\Enums\Permission as PermissionEnum;
use App\Enums\RoleType;
use App\Models\Membership;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\PermissionRegistrar;

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
                'approved_by' => null,
            ],
        );
        $globalResolver = resolve(SpatieTeamResolver::class);
        $globalResolver->setPermissionsTeamId(0);
        $admin->syncRoles([RoleType::ADMIN->value]);
        $globalResolver->setPermissionsTeamId(null);

        $adminRole = Role::where('name', RoleType::ADMIN->value)->where('guard_name', $guard)->first();
        if ($adminRole) {
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

        $teamA = Team::firstOrCreate(
            ['name' => 'Team A'],
            [
                'slug' => Str::slug('Team A-'.Str::random(4)),
                'is_personal' => false,
                'is_active' => true,
            ],
        );

        $teamB = Team::firstOrCreate(
            ['name' => 'Team B'],
            [
                'slug' => Str::slug('Team B-'.Str::random(4)),
                'is_personal' => false,
                'is_active' => true,
            ],
        );

        $this->ensurePermissionsForTeam($teamA->id, $guard);
        $this->ensurePermissionsForTeam($teamB->id, $guard);

        $this->ensureMembership($sicrano->id, $teamA->id, AppTeamRole::OWNER);
        $this->ensureMembership($beltrano->id, $teamA->id, AppTeamRole::MEMBER);

        $this->ensureMembership($beltrano->id, $teamB->id, AppTeamRole::OWNER);
        $this->ensureMembership($sicrano->id, $teamB->id, AppTeamRole::MEMBER);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    private function ensureMembership(int $userId, int $teamId, AppTeamRole $role): void
    {
        Membership::firstOrCreate(
            ['team_id' => $teamId, 'user_id' => $userId],
            ['role' => $role->value],
        );
    }

    /**
     * Garante que a role Owner do team contenha todas as permissões dos recursos.
     */
    private function ensurePermissionsForTeam(int $teamId, string $guard): void
    {
        $resources = ['media', 'users', 'authentication-log'];

        $teamResolver = resolve(SpatieTeamResolver::class);
        $teamResolver->setPermissionsTeamId($teamId);

        $ownerRole = RoleType::ensureOwnerRoleForTeam($teamId, $guard);
        RoleType::ensureUserRoleForTeam($teamId, $guard);

        foreach ($resources as $resource) {
            foreach (PermissionEnum::cases() as $permission) {
                $permissionName = $permission->for($resource);
                PermissionModel::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => $guard,
                ]);

                if (! $ownerRole->hasPermissionTo($permissionName, $guard)) {
                    $ownerRole->givePermissionTo($permissionName);
                }
            }
        }

        $teamResolver->setPermissionsTeamId(null);
    }
}
