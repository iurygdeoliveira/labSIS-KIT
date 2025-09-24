<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Tenancy\SpatieTeamResolver;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        // Apenas para o painel de usuário
        if ($panel && $panel->getId() === 'user') {
            $user = Filament::auth()->user();

            if ($user) {
                // Pega o primeiro tenant do usuário para definir o contexto
                $tenant = $user->tenants()->first();

                if ($tenant) {
                    $resolver = app(SpatieTeamResolver::class);
                    $resolver->setPermissionsTeamId($tenant->id);
                }
            }
        }

        return $next($request);
    }
}
