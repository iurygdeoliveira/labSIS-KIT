<?php

namespace App\Providers\Filament;

use App\Filament\Configurators\FilamentComponentsConfigurator;
use Filament\Pages\Dashboard;
use Filament\Panel;

class AdminPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);
        $panel = $this->applySharedPlugins($panel);

        // Particularidades do painel admin
        $panel = $panel
            ->bootUsing(function (): void {
                FilamentComponentsConfigurator::configure();
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
            ])
            ->tenant(null);

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
