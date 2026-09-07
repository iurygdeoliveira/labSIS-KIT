<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
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

        $currentTeam = Filament::getTenant();

        if ($currentTeam instanceof Organization) {
            $this->applyTeam($user, $currentTeam->getKey());

            return $next($request);
        }

        $routeTeamSlug = (string) ($request->route('tenant') ?? '');

        if ($routeTeamSlug !== '') {
            $routeTeam = Organization::query()->where('slug', $routeTeamSlug)->first();

            if ($routeTeam instanceof Organization && $user->canAccessTenant($routeTeam)) {
                $this->applyTeam($user, $routeTeam->getKey());

                return $next($request);
            }
        }

        if ($currentTeam === null) {
            /** @var Organization|null $fallback */
            $fallback = $user->organizations()
                ->where('is_active', true)
                ->orderBy('name', 'asc')
                ->first();

            if ($fallback !== null) {
                $this->applyTeam($user, $fallback->getKey());

                return $next($request);
            }
        }

        $this->applyTeam($user, 0);

        return $next($request);
    }

    private function applyTeam(User $user, int $teamId): void
    {
        resolve(SpatieTeamResolver::class)->setPermissionsTeamId($teamId);

        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');
    }
}
