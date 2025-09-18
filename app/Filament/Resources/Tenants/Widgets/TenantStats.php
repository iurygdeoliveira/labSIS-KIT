<?php

namespace App\Filament\Resources\Tenants\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class TenantStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        $totalTenants = Tenant::query()->count();
        $activeTenants = Tenant::query()->where('is_active', true)->count();
        $inactiveTenants = max($totalTenants - $activeTenants, 0);

        $totalUsersLinked = Tenant::query()
            ->withCount('users')
            ->get()
            ->sum('users_count');

        $avgUsersPerTenant = $totalTenants > 0
            ? round($totalUsersLinked / $totalTenants, 1)
            : 0;

        return [
            'total' => $totalTenants,
            'active' => $activeTenants,
            'inactive' => $inactiveTenants,
            'avgUsers' => $avgUsersPerTenant,
        ];
    }

    protected function getStats(): array
    {
        $summary = $this->summary;

        $activePct = $summary['total'] > 0 ? round(($summary['active'] / $summary['total']) * 100, 1) : 0;

        return [
            Stat::make('Tenants Ativos', number_format($summary['active']))
                ->description("{$activePct}% do total")
                ->icon('heroicon-c-check-badge')
                ->color('success'),

            Stat::make('Tenants Inativos', number_format($summary['inactive']))
                ->description('Sem uso ou suspensos')
                ->icon('heroicon-c-no-symbol')
                ->color('danger'),

            Stat::make('Total de Tenants', number_format($summary['total']))
                ->description('Cadastrados')
                ->icon('heroicon-c-building-office')
                ->color('secondary'),

            Stat::make('Usuários / Tenant', (string) $summary['avgUsers'])
                ->description('Usuários por tenant')
                ->icon('heroicon-c-user-group')
                ->color('warning'),
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'sm' => 2,
            'md' => 3,
            'xl' => 4,
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }
}
