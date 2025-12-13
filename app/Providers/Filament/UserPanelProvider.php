<?php

namespace App\Providers\Filament;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\CustomStats;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Tenant;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class UserPanelProvider extends BasePanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);
        $panel = $this->applySharedPlugins($panel);

        // Particularidades do painel user
        $panel = $panel
            ->tenant(Tenant::class, slugAttribute: 'uuid', ownershipRelationship: 'tenants')
            ->tenantMenu(true)
            ->resources([
                UserResource::class,
                MediaResource::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
                CustomStats::class,
            ])
            ->middleware([
                TeamSyncMiddleware::class,
            ]);

        return $panel;
    }

    protected function getPanelId(): string
    {
        return 'user';
    }

    protected function getPanelPath(): string
    {
        return 'user';
    }
}
