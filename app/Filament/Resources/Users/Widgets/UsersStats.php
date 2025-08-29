<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class UsersStats extends BaseWidget
{
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

        return [
            Stat::make('Usuários', (string) $summary['total'])
                ->icon('heroicon-c-user-group'),
            Stat::make('Suspensos', (string) $summary['suspended'])
                ->color('danger')
                ->icon('heroicon-c-no-symbol'),
            Stat::make('Verificados', (string) $summary['verified'])
                ->color('success')
                ->icon('heroicon-c-check-badge'),
        ];
    }
}
