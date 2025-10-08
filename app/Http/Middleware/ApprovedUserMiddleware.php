<?php

namespace App\Http\Middleware;

use App\Enums\RoleType;
use App\Filament\Pages\Auth\VerificationPending;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedUserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $request->routeIs('*logout')) {
            return $next($request);
        }

        // Administradores sempre têm acesso
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return $next($request);
        }

        // Se usuário está aprovado ou está acessando a página de verificação pendente
        if ($user->isApproved() || $request->routeIs('*.verification-pending')) {
            return $next($request);
        }

        // Permitir acesso a rotas de autenticação (login, registro, etc.)
        if ($request->routeIs('filament.auth.*')) {
            return $next($request);
        }

        // Redirecionar para página de verificação pendente
        return redirect()->to(VerificationPending::getUrl());
    }
}
