<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleType;
use App\Filament\Pages\Auth\VerificationPending;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Panel;
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

        if (! $user) {
            return $this->handleGuestAccess($request, $next);
        }

        // Se é rota de logout, permite acesso
        if ($request->routeIs('*logout')) {
            return $next($request);
        }

        $panel = Filament::getCurrentPanel();

        // 1. Verificar aprovação do usuário
        if (($response = $this->handlePendingVerification($user, $request, $next)) instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        // 2. Se estiver no painel de auth, redirecionar para o painel correto
        if (($response = $this->handleAuthPanelRedirect($user, $panel)) instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        // 3. Se não puder acessar o painel atual, redirecionar para o correto
        if (($response = $this->handleUnauthorizedPanelAccess($user, $panel)) instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        }

        return $next($request);
    }

    private function handleGuestAccess(Request $request, Closure $next): Response
    {
        $path = $request->path();

        // Verifica se é uma rota pública de autenticação
        $isPublicAuthRoute = $path === 'login'
            || $path === 'register'
            || str_starts_with($path, 'password-reset/');

        // Se for rota pública, permite acesso
        if ($isPublicAuthRoute) {
            return $next($request);
        }

        // Se for rota protegida, redireciona para o login central
        return redirect()->to('/login');
    }

    private function handlePendingVerification(User $user, Request $request, Closure $next): ?Response
    {
        // Administradores ignoram verificação
        if ($user->hasRole(RoleType::ADMIN->value)) {
            return null;
        }

        if (! $user->isApproved() && ! $request->routeIs('*.verification-pending')) {
            // Permitir acesso a rotas de autenticação (login, registro, etc.)
            if ($request->routeIs('filament.auth.*')) {
                return $next($request);
            }

            // Redirecionar para página de verificação pendente
            return redirect()->to(VerificationPending::getUrl());
        }

        return null;
    }

    private function handleAuthPanelRedirect(User $user, ?Panel $panel): ?Response
    {
        if ($panel instanceof \Filament\Panel && $panel->getId() === 'auth') {
            return redirect()->to($this->resolveRedirectUrl($user));
        }

        return null;
    }

    private function handleUnauthorizedPanelAccess(User $user, ?Panel $panel): ?Response
    {
        if (! $user->canAccessPanel($panel)) {
            return redirect()->to($this->resolveRedirectUrl($user));
        }

        return null;
    }

    private function resolveRedirectUrl(User $user): string
    {
        if ($user->canAccessPanel(Filament::getPanel('admin'))) {
            return '/admin';
        }

        if ($user->canAccessPanel(Filament::getPanel('user'))) {
            $firstTenant = $user->getTenants(Filament::getPanel('user'))->first();
            if ($firstTenant) {
                return '/user/'.$firstTenant->uuid;
            }

            return '/user';
        }

        // Fallback para home ou login se não tiver acesso a nada
        return '/login';
    }
}
