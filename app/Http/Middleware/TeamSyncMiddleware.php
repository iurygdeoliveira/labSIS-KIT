<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\SpatieTeamResolver;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamSyncMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if ($panel && $panel->getId() === 'user') {
            $user = Filament::auth()->user();

            if ($user instanceof User) {
                $routeTenantUuid = (string) ($request->route('tenant') ?? '');
                if ($routeTenantUuid !== '') {
                    $routeTenant = Tenant::query()->where('uuid', $routeTenantUuid)->first();

                    if ($routeTenant instanceof Tenant && $user->canAccessTenant($routeTenant)) {
                        $resolver = app(SpatieTeamResolver::class);
                        $resolver->setPermissionsTeamId($routeTenant->getKey());

                        return $next($request);
                    }
                }

                $currentTenant = Filament::getTenant();
                if ($currentTenant === null) {
                    $tenant = $user->tenants()
                        ->where('is_active', true)
                        ->orderBy('name', 'asc')
                        ->first();

                    if ($tenant instanceof Tenant) {
                        $resolver = app(SpatieTeamResolver::class);
                        $resolver->setPermissionsTeamId($tenant->getKey());

                        return $next($request);
                    }
                }

                $currentTenant = Filament::getTenant();
                if ($currentTenant instanceof Tenant) {
                    $resolver = app(SpatieTeamResolver::class);
                    $resolver->setPermissionsTeamId($currentTenant->getKey());
                } else {
                    $resolver = app(SpatieTeamResolver::class);
                    $resolver->setPermissionsTeamId(0);
                }
            }
        }

        return $next($request);
    }
}
