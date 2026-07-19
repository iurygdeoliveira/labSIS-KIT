<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Configurators\FilamentComponentsConfigurator;
use Filament\Pages\Dashboard;
use Filament\Panel;
use WallaceMartinss\FilamentSecurity\FilamentSecurityPlugin;

class AdminPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel admin
        $panel = $panel
            ->bootUsing(function (): void {
                FilamentComponentsConfigurator::configure();
            })
            ->plugin(
                FilamentSecurityPlugin::make()
                    ->disposableEmailProtection()
                    ->honeypotProtection()
                    ->cloudflareBlocking()
                    ->eventLog(false)
            )

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
