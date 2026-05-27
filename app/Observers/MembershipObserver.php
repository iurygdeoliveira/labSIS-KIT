<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AppTeamRole;
use App\Enums\RoleType;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Support\FilamentStatsCache;
use Spatie\Permission\PermissionRegistrar;

/**
 * Sincroniza o pivot `team_members.role` com as roles do Spatie por team.
 *
 * Spatie é a fonte da verdade da autorização. Este observer garante que
 * qualquer alteração feita via UI do FilaTeams (criar membership, mudar
 * role, remover membership) reflita imediatamente em `model_has_roles`.
 */
class MembershipObserver
{
    public function created(Membership $membership): void
    {
        $this->applyRole($membership);
        $this->forgetStatsCaches();
    }

    public function updated(Membership $membership): void
    {
        if (! $membership->wasChanged('role')) {
            return;
        }

        $this->applyRole($membership);
        $this->forgetStatsCaches();
    }

    public function deleted(Membership $membership): void
    {
        [$user, $team] = $this->resolveMembershipActors($membership);

        if ($user === null || $team === null) {
            return;
        }

        $user->removeAllOwnerRolesFromTeam($team);
        $user->removeAllUserRolesFromTeam($team);

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->forgetStatsCaches();
    }

    private function forgetStatsCaches(): void
    {
        FilamentStatsCache::forgetTeams();
        FilamentStatsCache::forgetUsers();
    }

    private function applyRole(Membership $membership): void
    {
        [$user, $team] = $this->resolveMembershipActors($membership);

        if ($user === null || $team === null) {
            return;
        }

        $pivotRole = $membership->role;

        $user->removeAllOwnerRolesFromTeam($team);
        $user->removeAllUserRolesFromTeam($team);

        $spatieRole = match ($pivotRole) {
            AppTeamRole::OWNER => RoleType::ensureOwnerRoleForTeam($team->id, $this->guard()),
            AppTeamRole::MEMBER => RoleType::ensureUserRoleForTeam($team->id, $this->guard()),
        };

        $user->assignRoleInTeam($spatieRole, $team);

        resolve(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array{0: User|null, 1: Team|null}
     */
    private function resolveMembershipActors(Membership $membership): array
    {
        return [
            User::query()->find($membership->user_id),
            Team::query()->find($membership->team_id),
        ];
    }

    private function guard(): string
    {
        return (string) config('auth.defaults.guard', 'web');
    }
}
