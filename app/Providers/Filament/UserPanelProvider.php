<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\CustomStats;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Models\Team;
use Filament\Pages\Dashboard;
use Filament\Panel;
use LaravelDaily\FilaTeams\Pages\CreateTeamPage;
use LaravelDaily\FilaTeams\Pages\EditTeam;

class UserPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel user
        $panel = $panel
            ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'teams')
            ->tenantRegistration(CreateTeamPage::class)
            ->tenantProfile(EditTeam::class)
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
