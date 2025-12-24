<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\AccountSuspended;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Auth\VerificationPending;
use App\Http\Middleware\EnsureSecurityHeaders;
use App\Http\Middleware\RedirectGuestsToCentralLoginMiddleware;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AuthPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('auth')
            ->path('')
            ->default()
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->brandLogo(fn () => view('filament.auth.logo_auth'))
            ->brandLogoHeight('8rem')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->authGuard('web')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                VerificationPending::class,
                AccountSuspended::class,
            ])
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(
                RequestPasswordReset::class,
            )
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->recoverable()
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                RedirectGuestsToCentralLoginMiddleware::class,
                EnsureSecurityHeaders::class,
            ]);

    }
}
