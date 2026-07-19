<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\RoleType;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Support\FilamentStatsCache;
use Spatie\Permission\PermissionRegistrar;

class OrganizationUserObserver
{
    public function created(OrganizationUser $pivot): void
    {
        $this->applyRole($pivot);
        $this->forgetStatsCaches();
    }

    public function updated(OrganizationUser $pivot): void
    {
        if (! $pivot->wasChanged('role')) {
            return;
        }

        $this->applyRole($pivot);
        $this->forgetStatsCaches();
    }

    public function deleted(OrganizationUser $pivot): void
    {
        [$user, $organization] = $this->resolveActors($pivot);

        if ($user === null || $organization === null) {
            return;
        }

        $user->removeAllOwnerRolesFromTeam($organization);
        $user->removeAllUserRolesFromTeam($organization);

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->forgetStatsCaches();
    }

    private function forgetStatsCaches(): void
    {
        FilamentStatsCache::forgetTeams();
        FilamentStatsCache::forgetUsers();
    }

    private function applyRole(OrganizationUser $pivot): void
    {
        [$user, $organization] = $this->resolveActors($pivot);

        if ($user === null || $organization === null) {
            return;
        }

        $user->removeAllOwnerRolesFromTeam($organization);
        $user->removeAllUserRolesFromTeam($organization);

        $spatieRole = match ($pivot->role) {
            'owner', 'admin' => RoleType::ensureOwnerRoleForTeam($organization->id, $this->guard()),
            default => RoleType::ensureUserRoleForTeam($organization->id, $this->guard()),
        };

        $user->assignRoleInTeam($spatieRole, $organization);

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array{0: User|null, 1: Organization|null}
     */
    private function resolveActors(OrganizationUser $pivot): array
    {
        return [
            User::query()->find($pivot->user_id),
            Organization::query()->find($pivot->organization_id),
        ];
    }

    private function guard(): string
    {
        return (string) config('auth.defaults.guard', 'web');
    }
}
