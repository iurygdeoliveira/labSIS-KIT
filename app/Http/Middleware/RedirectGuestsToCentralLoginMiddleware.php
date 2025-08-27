<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectGuestsToCentralLoginMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Filament::auth()->check()) {
            return $next($request);
        }

        $path = $request->path();

        // Permitir acessar páginas públicas e as próprias rotas do painel de autenticação
        $isPublicAuthRoute = $path === 'login'
            || $path === 'register'
            || str_starts_with($path, 'password-reset')
            || str_starts_with($path, 'email/verify')
            || str_starts_with($path, 'email/change');

        if ($isPublicAuthRoute) {
            return $next($request);
        }

        // Se o usuário convidado tentou acessar qualquer outro painel, redireciona para /login
        return redirect()->to('/login');
    }
}
