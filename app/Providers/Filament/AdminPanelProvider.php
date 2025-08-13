<?php

namespace App\Providers\Filament;

use App\Filament\Configurators\FilamentComponentsConfigurator;
use Filafly\Themes\Brisk\BriskTheme;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->spa()
            ->databaseTransactions()
            ->id('admin')
            ->path('admin')
            ->bootUsing(function (): void {
                FilamentComponentsConfigurator::configure();
            })
            ->login()
            ->registration()
            ->profile()
            ->passwordReset()
            ->emailVerification()
            ->colors([
                'primary' => '#014029',
                'secondary' => '#6b7a91',
                'danger' => '#D93223',
                'warning' => '#F28907',
                'success' => '#2eb347',
                'info' => '#1F8C4E',
                'light' => '#f7f8fc',
                'disabled' => '#a2a2ac',
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->sidebarWidth('15rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(BriskTheme::make()->withoutSuggestedFont());
    }
}
