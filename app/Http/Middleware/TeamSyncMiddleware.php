<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Team;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mantém o resolver de team do Spatie sincronizado com o tenant ativo do
 * Filament e invalida caches relacionais para evitar roles vazadas entre
 * teams na mesma requisição.
 *
 * O FilaTeams configura a tenancy do Filament, mas não toca no Spatie. Este
 * middleware faz a ponte entre os dois.
 */
class TeamSyncMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel || $panel->getId() !== 'user') {
            return $next($request);
        }

        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return $next($request);
        }

        $routeTeamSlug = (string) ($request->route('tenant') ?? '');

        if ($routeTeamSlug !== '') {
            $routeTeam = Team::query()->where('slug', $routeTeamSlug)->first();

            if ($routeTeam instanceof Team && $user->canAccessTenant($routeTeam)) {
                $this->applyTeam($user, $routeTeam->getKey());

                return $next($request);
            }
        }

        $currentTeam = Filament::getTenant();

        if ($currentTeam === null) {
            /** @var Team|null $fallback */
            $fallback = $user->teams()
                ->where('is_active', true)
                ->orderBy('name', 'asc')
                ->first();

            if ($fallback !== null) {
                $this->applyTeam($user, $fallback->getKey());

                return $next($request);
            }
        }

        $teamId = $currentTeam instanceof Team ? $currentTeam->getKey() : 0;
        $this->applyTeam($user, $teamId);

        return $next($request);
    }

    private function applyTeam(User $user, int $teamId): void
    {
        resolve(SpatieTeamResolver::class)->setPermissionsTeamId($teamId);

        if ($teamId !== 0) {
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');
            resolve(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
