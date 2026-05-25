<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AppTeamRole;
use App\Enums\RoleType;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
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
    }

    public function updated(Membership $membership): void
    {
        if (! $membership->wasChanged('role')) {
            return;
        }

        $this->applyRole($membership);
    }

    public function deleted(Membership $membership): void
    {
        $user = $membership->user;
        $team = $membership->team;

        if (! $user instanceof User || ! $team instanceof Team) {
            return;
        }

        $user->removeAllOwnerRolesFromTeam($team);
        $user->removeAllUserRolesFromTeam($team);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function applyRole(Membership $membership): void
    {
        $user = $membership->user;
        $team = $membership->team;

        if (! $user instanceof User || ! $team instanceof Team) {
            return;
        }

        $pivotRole = $membership->role instanceof AppTeamRole
            ? $membership->role
            : AppTeamRole::tryFrom((string) $membership->role);

        if (! $pivotRole instanceof AppTeamRole) {
            return;
        }

        $user->removeAllOwnerRolesFromTeam($team);
        $user->removeAllUserRolesFromTeam($team);

        $spatieRole = match ($pivotRole) {
            AppTeamRole::OWNER => RoleType::ensureOwnerRoleForTeam($team->id, $this->guard()),
            AppTeamRole::MEMBER => RoleType::ensureUserRoleForTeam($team->id, $this->guard()),
        };

        $user->assignRoleInTeam($spatieRole, $team);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function guard(): string
    {
        return (string) config('auth.defaults.guard', 'web');
    }
}
