<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class UsersStats extends BaseWidget
{
    protected static ?string $color = 'primary';

    #[Computed]
    protected function summary(): array
    {
        $totalUsers = User::query()->count();
        $suspendedUsers = User::query()->where('is_suspended', true)->count();
        $verifiedUsers = User::query()->whereNotNull('email_verified_at')->count();

        return [
            'total' => $totalUsers,
            'suspended' => $suspendedUsers,
            'verified' => $verifiedUsers,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;

        // Calcular percentuais
        $verifiedPercentage = $summary['total'] > 0
            ? round(($summary['verified'] / $summary['total']) * 100, 1)
            : 0;

        $suspendedPercentage = $summary['total'] > 0
            ? round(($summary['suspended'] / $summary['total']) * 100, 1)
            : 0;

        return [
            Stat::make('Total de Usuários', number_format($summary['verified']))
                ->description('Usuários Cadastrados no sistema')
                ->icon('heroicon-c-user-group')
                ->color('secondary'),

            Stat::make('Usuários Verificados', number_format($summary['verified']))
                ->description("{$verifiedPercentage}% do total")
                ->icon('heroicon-c-check-badge')
                ->color('primary'),

            Stat::make('Usuários Suspensos', number_format($summary['suspended']))
                ->description("{$suspendedPercentage}% do total")
                ->icon('heroicon-c-no-symbol')
                ->color('danger'),
        ];
    }
}
