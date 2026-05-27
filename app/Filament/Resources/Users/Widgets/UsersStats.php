<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Support\FilamentStatsCache;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

/**
 * @property-read array $summary
 * @property-read array $percentages
 */
class UsersStats extends BaseWidget
{
    protected static ?string $color = 'primary';

    protected ?string $pollingInterval = null;

    #[Computed]
    protected function summary(): array
    {
        $stats = FilamentStatsCache::users();

        return [
            'total' => $stats['total'],
            'suspended' => $stats['suspended'],
            'verified' => $stats['verified'],
            'unapproved' => $stats['unapproved'],
        ];
    }

    #[Computed]
    protected function percentages(): array
    {
        $total = $this->summary['total'];

        return [
            'verified' => $total > 0 ? round(($this->summary['verified'] / $total) * 100, 1) : 0,
            'suspended' => $total > 0 ? round(($this->summary['suspended'] / $total) * 100, 1) : 0,
            'unapproved' => $total > 0 ? round(($this->summary['unapproved'] / $total) * 100, 1) : 0,
        ];
    }

    #[\Override]
    protected function getStats(): array
    {
        $summary = $this->summary;
        $percentages = $this->percentages;

        return [
            Stat::make('Total de Usuários', number_format($summary['total']))
                ->description('Cadastrados no sistema')
                ->icon(Heroicon::UserGroup)
                ->color('info'),

            Stat::make('Usuários Verificados', number_format($summary['verified']))
                ->description("{$percentages['verified']}% do total")
                ->icon(Heroicon::CheckBadge)
                ->color('primary'),

            Stat::make('Usuários Suspensos', number_format($summary['suspended']))
                ->description("{$percentages['suspended']}% do total")
                ->icon(Heroicon::NoSymbol)
                ->color('warning'),

            Stat::make('Usuários Não Aprovados', number_format($summary['unapproved']))
                ->description("{$percentages['unapproved']}% do total")
                ->icon(Heroicon::Clock)
                ->color('danger'),
        ];
    }
}
