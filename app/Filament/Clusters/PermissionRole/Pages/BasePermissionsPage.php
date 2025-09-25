<?php

declare(strict_types=1);

namespace App\Filament\Clusters\PermissionRole\Pages;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Filament\Clusters\PermissionRole\PermissionRoleCluster;
use App\Models\Role;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission as PermissionModel;

abstract class BasePermissionsPage extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $cluster = PermissionRoleCluster::class;

    protected static ?string $title = 'Controle de Permissões';

    abstract public function getResourceName(): string;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Role::query()
                    ->with(['tenant'])
                    ->whereNotNull('team_id')
                    ->where('team_id', '!=', 0)
                    ->where('name', '!=', RoleType::ADMIN->value)
            )
            ->groups([
                Group::make('tenant.name')
                    ->label('Tenant'),
            ])
            ->defaultGroup('tenant.name')
            ->groupingSettingsHidden()
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
                    ->formatStateUsing(static function (string $state): string {
                        $role = RoleType::tryFrom($state);

                        return $role ? $role->getLabel() : $state;
                    }),
                $this->makeToggleColumnForAction(Permission::VIEW->value, 'Visualizar'),
                $this->makeToggleColumnForAction(Permission::CREATE->value, 'Criar'),
                $this->makeToggleColumnForAction(Permission::UPDATE->value, 'Editar'),
                $this->makeToggleColumnForAction(Permission::DELETE->value, 'Apagar'),
            ]);
    }

    private function makeToggleColumnForAction(string $action, string $label): ToggleColumn
    {
        $permissionName = "{$this->getResourceName()}.{$action}";

        return ToggleColumn::make($permissionName)
            ->label($label)
            ->onColor('primary')
            ->offColor('danger')
            ->onIcon('heroicon-c-check')
            ->offIcon('heroicon-c-x-mark')
            ->getStateUsing(static function (Role $record) use ($permissionName): bool {
                // Garante que a permissão exista antes de checar estado, evitando exceção
                PermissionModel::findOrCreate($permissionName, config('auth.defaults.guard', 'web'));

                return $record->hasPermissionTo($permissionName);
            })
            ->updateStateUsing(static function (Role $record, $state) use ($permissionName): void {
                PermissionModel::findOrCreate($permissionName, config('auth.defaults.guard', 'web'));

                if ($state) {
                    $record->givePermissionTo($permissionName);
                } else {
                    $record->revokePermissionTo($permissionName);
                }
            });
    }
}
