<?php

declare(strict_types=1);

namespace App\Providers\Filament;

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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function applySharedPlugins(Panel $panel): Panel
    {
        return $panel
            ->plugin(BriskTheme::make()->withoutSuggestedFont())
            ->plugin(
                FilamentEditProfilePlugin::make()
                    ->setNavigationLabel('Editar Perfil')
                    ->setNavigationGroup('Configurações')
                    ->setIcon('heroicon-s-adjustments-horizontal')
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
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
                    ->withLinks([
                        ['title' => 'Precisa de Software ?', 'url' => 'https://www.labsis.dev.br'],
                        ['title' => 'LabSIS', 'url' => 'https://www.labsis.dev.br'],
                    ]),
            );
    }

    abstract protected function getPanelId(): string;

    abstract protected function getPanelPath(): string;
}
