<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureSecurityHeaders;
use App\Http\Middleware\RedirectGuestsToCentralLoginMiddleware;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use Devonab\FilamentEasyFooter\EasyFooterPlugin;
use Filafly\Themes\Brisk\BriskTheme;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

abstract class BasePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id($this->getPanelId())
            ->path($this->getPanelPath())
            ->spa()
            ->globalSearch(false)
            ->databaseTransactions()
            ->darkMode(false)
            ->defaultThemeMode(ThemeMode::Light)
            ->brandLogo(fn () => view('filament.auth.logo_base'))
            ->brandLogoHeight('2rem')
            ->multiFactorAuthentication(
                AppAuthentication::make()
                    ->recoverable()
            )
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarWidth('15rem')
            ->maxContentWidth(Width::Full)
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
                RedirectToProperPanelMiddleware::class,
                EnsureSecurityHeaders::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function applySharedPlugins(Panel $panel): Panel
    {
        return $panel
            ->plugin(
                FilamentAuthenticationLogPlugin::make()
            )
            ->plugin(BriskTheme::make()->withoutSuggestedFont())
            ->plugin(
                FilamentEditProfilePlugin::make()
                    ->setNavigationLabel('Editar Perfil')
                    ->setNavigationGroup('')
                    ->setIcon('heroicon-s-adjustments-horizontal')
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatar',
                        rules: 'mimes:png,jpg,jpeg|max:1024'
                    )
                    ->shouldShowEmailForm()
                    ->shouldShowDeleteAccountForm(false)
                    ->shouldShowMultiFactorAuthentication()
            )
            ->plugin(
                EasyFooterPlugin::make()
                    ->footerEnabled()
                    ->withFooterPosition('footer')
                    ->withGithub(showLogo: true, showUrl: true)
                    ->withLogo(
                        asset('images/LabSIS_painel.png'),
                        'https://www.labsis.dev.br'
                    )
                    ->withLinks([
                        ['title' => 'Precisa de Software ?', 'url' => 'https://www.labsis.dev.br'],
                    ])
            );
    }

    abstract protected function getPanelId(): string;

    abstract protected function getPanelPath(): string;
}
