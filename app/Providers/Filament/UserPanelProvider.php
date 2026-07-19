<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\CustomStats;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Organization;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Guidance\FilamentTenantMembers\Filament\OrganizationPanel\Pages\Tenancy\RegisterOrganization;
use Guidance\FilamentTenantMembers\FilamentTenantMembersPlugin;

class UserPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel user
        $panel = $panel
            ->tenant(Organization::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterOrganization::class)
            ->plugin(FilamentTenantMembersPlugin::make())
            ->tenantMenu(true)
            ->resources([
                UserResource::class,
                MediaResource::class,
                AuthenticationLogResource::class,
            ])
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                CustomStats::class,
            ])
            ->tenantMiddleware([
                TeamSyncMiddleware::class,
            ], isPersistent: true);

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
