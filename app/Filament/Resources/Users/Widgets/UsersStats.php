<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Enums\RoleType;
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
        $baseQuery = User::query()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', RoleType::ADMIN->value);
            });

        $totalUsers = $baseQuery->count();
        $suspendedUsers = $baseQuery->where('is_suspended', true)->count();
        $verifiedUsers = $baseQuery->whereNotNull('email_verified_at')->count();

        return [
            'total' => $totalUsers,
            'suspended' => $suspendedUsers,
            'verified' => $verifiedUsers,
        ];
    }

    #[Computed]
    protected function percentages(): array
    {
        $summary = $this->summary;

        return [
            'verified' => $summary['total'] > 0
                ? round(($summary['verified'] / $summary['total']) * 100, 1)
                : 0,
            'suspended' => $summary['total'] > 0
                ? round(($summary['suspended'] / $summary['total']) * 100, 1)
                : 0,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;
        $percentages = $this->percentages;

        return [
            Stat::make('Total de Usuários', number_format($summary['total']))
                ->description('Usuários Cadastrados no sistema')
                ->icon('heroicon-c-user-group')
                ->color('secondary'),

            Stat::make('Usuários Verificados', number_format($summary['verified']))
                ->description("{$percentages['verified']}% do total")
                ->icon('heroicon-c-check-badge')
                ->color('primary'),

            Stat::make('Usuários Suspensos', number_format($summary['suspended']))
                ->description("{$percentages['suspended']}% do total")
                ->icon('heroicon-c-no-symbol')
                ->color('danger'),
        ];
    }
}
