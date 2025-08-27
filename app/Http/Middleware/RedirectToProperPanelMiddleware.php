<?php

declare(strict_types=1);

namespace App\Http\Middleware;

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

        $panel = Filament::getCurrentPanel();

        // Se o usuário estiver autenticado e tentar acessar o painel de autenticação,
        // redireciona imediatamente para o painel apropriado conforme suas permissões.
        if ($panel && $panel->getId() === 'auth') {
            if ($user->canAccessPanel(Filament::getPanel('admin'))) {
                return redirect()->to('/admin');
            }

            if ($user->canAccessPanel(Filament::getPanel('user'))) {
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
                return redirect()->to('/user');
            }
        }

        return $next($request);
    }
}
