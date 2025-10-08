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
    protected function baseQuery()
    {
        return User::query()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', RoleType::ADMIN->value);
            });
    }

    #[Computed]
    protected function totalUsers(): int
    {
        return $this->baseQuery->count();
    }

    #[Computed]
    protected function suspendedUsers(): int
    {
        return (clone $this->baseQuery)
            ->where('is_suspended', true)
            ->count();
    }

    #[Computed]
    protected function verifiedUsers(): int
    {
        return (clone $this->baseQuery)
            ->whereNotNull('email_verified_at')
            ->count();
    }

    #[Computed]
    protected function unapprovedUsers(): int
    {
        return (clone $this->baseQuery)
            ->where('is_suspended', false)
            ->whereNull('approved_at')
            ->count();
    }

    #[Computed]
    protected function summary(): array
    {
        return [
            'total' => $this->totalUsers,
            'suspended' => $this->suspendedUsers,
            'verified' => $this->verifiedUsers,
            'unapproved' => $this->unapprovedUsers,
        ];
    }

    #[Computed]
    protected function percentages(): array
    {
        $total = $this->totalUsers;

        return [
            'verified' => $total > 0 ? round(($this->verifiedUsers / $total) * 100, 1) : 0,
            'suspended' => $total > 0 ? round(($this->suspendedUsers / $total) * 100, 1) : 0,
            'unapproved' => $total > 0 ? round(($this->unapprovedUsers / $total) * 100, 1) : 0,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;
        $percentages = $this->percentages;

        return [
            Stat::make('Total de Usuários', number_format($summary['total']))
                ->description('Cadastrados no sistema')
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

            Stat::make('Usuários Não Aprovados', number_format($summary['unapproved']))
                ->description("{$percentages['unapproved']}% do total")
                ->icon('heroicon-c-clock')
                ->color('danger'),
        ];
    }
}
