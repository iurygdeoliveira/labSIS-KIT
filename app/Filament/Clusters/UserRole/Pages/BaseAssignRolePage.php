<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\TenantUser;
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
        return $table
            ->query(
                TenantUser::query()
                    ->with(['user', 'tenant'])
                    ->whereHas(
                        'user',
                        fn ($query) => $query->when(
                            Filament::auth()->check(),
                            fn ($query) => $query->whereKeyNot(Filament::auth()->id())
                        )
                    )
            )
            ->groups([
                Group::make('tenant.name')
                    ->label('Tenant'),
            ])
            ->defaultGroup('tenant.name')
            ->groupingSettingsHidden()
            ->columns(array_merge([
                TextColumn::make('user.name')
                    ->label('Nome'),
                TextColumn::make('user.email')
                    ->label('E-mail'),
            ], $this->getExtraColumns()));
    }
}
