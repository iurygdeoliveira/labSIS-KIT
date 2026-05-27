<?php

namespace App\Filament\Resources\Teams\Widgets;

use App\Support\FilamentStatsCache;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

/**
 * @property-read array $summary
 */
class TeamStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[Computed]
    protected function summary(): array
    {
        $stats = FilamentStatsCache::teams();

        return [
            'total' => $stats['total'],
            'active' => $stats['active_flag'],
            'inactive' => $stats['inactive_flag'],
            'avgUsers' => $stats['avg_users_per_team'],
        ];
    }

    #[\Override]
    protected function getStats(): array
    {
        $summary = $this->summary;

        $activePct = $summary['total'] > 0 ? round(($summary['active'] / $summary['total']) * 100, 1) : 0;

        return [
            Stat::make('Teams Ativos', number_format($summary['active']))
                ->description("{$activePct}% do total")
                ->icon(Heroicon::CheckBadge)
                ->color('primary'),

            Stat::make('Teams Inativos', number_format($summary['inactive']))
                ->description('Sem uso ou suspensos')
                ->icon(Heroicon::NoSymbol)
                ->color('danger'),

            Stat::make('Total de Teams', number_format($summary['total']))
                ->description('Cadastrados')
                ->icon(Heroicon::BuildingOffice)
                ->color('secondary'),

            Stat::make('Usuários / Team', (string) $summary['avgUsers'])
                ->description('Usuários por team')
                ->icon(Heroicon::UserGroup)
                ->color('warning'),
        ];
    }

    #[\Override]
    protected function getColumns(): int|array
    {
        return [
            'sm' => 2,
            'md' => 3,
            'xl' => 4,
        ];
    }

    #[\Override]
    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
