<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleType;
use App\Filament\Pages\Auth\VerificationPending;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToProperPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = Filament::auth()->user();

        // Se não há usuário logado, permite o acesso (páginas de login, register, reset)
        if (! $user) {
            return $next($request);
        }

        // Se é rota de logout, permite acesso
        if ($request->routeIs('*logout')) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        // Verificar aprovação do usuário (exceto administradores)
        if (method_exists($user, 'hasRole') && ! $user->hasRole(RoleType::ADMIN->value)) {
            // Se usuário não está aprovado e não está acessando página de verificação pendente
            if (method_exists($user, 'isApproved') && ! $user->isApproved() && ! $request->routeIs('*.verification-pending')) {
                // Permitir acesso a rotas de autenticação (login, registro, etc.)
                if ($request->routeIs('filament.auth.*')) {
                    return $next($request);
                }

                // Redirecionar para página de verificação pendente
                return redirect()->to(VerificationPending::getUrl());
            }
        }

        // Se o usuário estiver autenticado e tentar acessar o painel de autenticação,
        // redireciona imediatamente para o painel apropriado conforme suas permissões
        if ($panel && $panel->getId() === 'auth') {
            if ($user->canAccessPanel(Filament::getPanel('admin'))) {
                return redirect()->to('/admin');
            }

            if ($user->canAccessPanel(Filament::getPanel('user'))) {
                $firstTenant = $user->getTenants(Filament::getPanel('user'))->first();
                if ($firstTenant) {
                    return redirect()->to('/user/'.$firstTenant->uuid);
                }

                return redirect()->to('/user');
            }
        }

        // Se o usuário não pode acessar o painel atual, redireciona para o correto
        if (! $user->canAccessPanel($panel)) {
            // Determina para qual painel redirecionar baseado no role
            if ($user->canAccessPanel(Filament::getPanel('admin'))) {
                return redirect()->to('/admin');
            }

            if ($user->canAccessPanel(Filament::getPanel('user'))) {
                $firstTenant = $user->getTenants(Filament::getPanel('user'))->first();
                if ($firstTenant) {
                    return redirect()->to('/user/'.$firstTenant->uuid);
                }

                return redirect()->to('/user');
            }
        }

        return $next($request);
    }
}
