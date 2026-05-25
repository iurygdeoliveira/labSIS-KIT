<?php

namespace App\Filament\Resources\Users\Widgets;

use App\Enums\RoleType;
use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

/**
 * @property-read Builder $baseQuery
 * @property-read int $totalUsers
 * @property-read int $suspendedUsers
 * @property-read int $verifiedUsers
 * @property-read int $unapprovedUsers
 * @property-read array $summary
 * @property-read array $percentages
 */
class UsersStats extends BaseWidget
{
    protected static ?string $color = 'primary';

    protected ?string $pollingInterval = null;

    #[Computed]
    protected function baseQuery()
    {
        return User::query()
            ->whereDoesntHave('roles', function ($query): void {
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
            ->where('is_approved', false)
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
