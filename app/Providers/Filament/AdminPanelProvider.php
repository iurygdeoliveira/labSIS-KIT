<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Support\NotificationCenter\NotificationCategory;
use App\Support\NotificationCenter\NotificationCenterManager;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;

class AdminPanelProvider extends BasePanelProvider
{
    #[\Override]
    public function panel(Panel $panel): Panel
    {
        // Configurações compartilhadas (Base define id/path via getPanelId/getPanelPath)
        $panel = parent::panel($panel);

        NotificationCenterManager::registerCategories('admin', [
            NotificationCategory::make('system')
                ->label('Sistema')
                ->icon(Heroicon::Cog6Tooth)
                ->color(Color::Gray)
                ->order(1),
            NotificationCategory::make('security')
                ->label('Segurança')
                ->icon(Heroicon::ShieldCheck)
                ->color(Color::Red)
                ->order(2),
            NotificationCategory::make('backups')
                ->label('Backups')
                ->icon(Heroicon::ArchiveBox)
                ->color(Color::Amber)
                ->order(3),
        ]);

        // Particularidades do painel admin
        $panel = $panel
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
