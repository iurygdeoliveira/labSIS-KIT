<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Filament\Clusters\UserRole\UserRoleCluster;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AssignRoles extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected string $view = 'filament.clusters.user-role.pages.assign-roles';

    protected static ?string $cluster = UserRoleCluster::class;

    protected static ?string $title = 'Atribuir Funções';

    protected static ?string $navigationLabel = 'Atribuir Funções';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->when(Filament::auth()->check(), fn ($query) => $query->whereKeyNot(Filament::auth()->id()))
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nome'),
                ToggleColumn::make('role_admin')
                    ->label(RoleType::ADMIN->value)
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(static function (User $record): bool {
                        return $record->hasRole(RoleType::ADMIN->value);
                    })
                    ->updateStateUsing(static function (User $record, bool $state): void {
                        if ($state) {
                            $record->syncRoles([RoleType::ADMIN->value]);

                            return;
                        }

                        $record->removeRole(RoleType::ADMIN->value);
                    }),
                ToggleColumn::make('role_user')
                    ->label(RoleType::USER->value)
                    ->onColor('primary')
                    ->offColor('danger')
                    ->onIcon('heroicon-c-check')
                    ->offIcon('heroicon-c-x-mark')
                    ->getStateUsing(static function (User $record): bool {
                        return $record->hasRole(RoleType::USER->value);
                    })
                    ->updateStateUsing(static function (User $record, bool $state): void {
                        if ($state) {
                            $record->syncRoles([RoleType::USER->value]);

                            return;
                        }

                        $record->removeRole(RoleType::USER->value);
                    }),
            ]);
    }
}
