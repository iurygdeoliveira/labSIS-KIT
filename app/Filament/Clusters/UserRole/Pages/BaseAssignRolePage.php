<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\TenantUser;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

abstract class BaseAssignRolePage extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $cluster = UserRoleCluster::class;

    abstract protected function getExtraColumns(): array;

    public function table(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $currentTenant = Filament::getTenant();

        $query = TenantUser::query()
            ->with(['user', 'tenant']);

        // Se não for admin, filtra apenas o tenant atual
        if (! $isAdmin && $currentTenant instanceof \App\Models\Tenant) {
            $query->where('tenant_id', $currentTenant->getKey());
        }

        $tableConfig = $table->query($query);

        // Se for admin, agrupa por tenant
        if ($isAdmin) {
            $tableConfig = $tableConfig
                ->groups([
                    Group::make('tenant.name')
                        ->label('Tenant'),
                ])
                ->defaultGroup('tenant.name')
                ->groupingSettingsHidden();
        }

        return $tableConfig->columns(array_merge([
            TextColumn::make('user.name')
                ->label('Nome'),
            TextColumn::make('user.email')
                ->label('E-mail'),
        ], $this->getExtraColumns()));
    }
}
