<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\AcceptInvite;
use App\Filament\Pages\Organization\GeneralSettings;
use App\Filament\Pages\Organization\Members;
use App\Filament\Pages\Organization\RegisterOrganization;
use App\Filament\Resources\Authentication\AuthenticationLogResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\CustomStats;
use App\Http\Middleware\TeamSyncMiddleware;
use App\Livewire\Organization\ListInvitations;
use App\Livewire\Organization\ListMembers;
use App\Models\Organization;
use Filament\Pages\Dashboard;
use Filament\Panel;

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
            ->tenantMenu(true)
            ->pages([
                Dashboard::class,
                GeneralSettings::class,
                Members::class,
                AcceptInvite::class,
            ])
            ->livewireComponents([
                ListMembers::class,
                ListInvitations::class,
            ])
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
