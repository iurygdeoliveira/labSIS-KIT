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
use App\Http\Middleware\TeamSyncMiddleware;
use App\Livewire\Organization\ListInvitations;
use App\Livewire\Organization\ListMembers;
use App\Models\Organization;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Prodstarter\FilamentNotificationCenter\FilamentNotificationCenterPlugin;
use Prodstarter\FilamentNotificationCenter\NotificationCenterCategory;

class UserPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        // Particularidades do painel user
        $panel = $panel
            ->plugins([
                FilamentNotificationCenterPlugin::make()->categories([
                    NotificationCenterCategory::make('tenant')
                        ->label('Organização')
                        ->icon(Heroicon::Users)
                        ->color(Color::Blue)
                        ->order(1),
                    NotificationCenterCategory::make('media')
                        ->label('Mídias')
                        ->icon(Heroicon::Photo)
                        ->color(Color::Emerald)
                        ->order(2),
                    NotificationCenterCategory::make('billing')
                        ->label('Faturamento')
                        ->icon(Heroicon::CreditCard)
                        ->color(Color::Amber)
                        ->order(3),
                ]),
            ])
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
