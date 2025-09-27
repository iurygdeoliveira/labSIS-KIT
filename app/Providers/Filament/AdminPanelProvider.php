<?php

namespace App\Providers\Filament;

use App\Filament\Configurators\FilamentComponentsConfigurator;
use App\Filament\Pages\Permission as PermissionPage;
use App\Filament\Resources\Tenants\TenantResource;
use App\Filament\Widgets\SystemStats;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class AdminPanelProvider extends BasePanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);
        $panel = $this->applySharedPlugins($panel);

        // Particularidades do painel admin
        $panel = $panel
            ->default()
            ->bootUsing(function (): void {
                FilamentComponentsConfigurator::configure();
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
                PermissionPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
                SystemStats::class,
            ])
            ->tenant(null) // desabilita tenancy e o menu de tenant no painel admin
            ->resources([
                TenantResource::class,
            ]);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'admin';
    }

    protected function getPanelPath(): string
    {
        return 'admin';
    }
}
