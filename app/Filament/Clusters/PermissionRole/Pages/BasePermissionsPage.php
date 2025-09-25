<?php

declare(strict_types=1);

namespace App\Filament\Clusters\PermissionRole\Pages;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Filament\Clusters\PermissionRole\PermissionRoleCluster;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

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
                    ->select(['roles.*', 'tenants.name as tenant_name'])
                    ->leftJoin('tenants', 'tenants.id', '=', 'roles.team_id')
                    ->whereNotNull('roles.team_id')
                    ->where('roles.team_id', '!=', 0)
                    ->where('roles.name', '!=', RoleType::ADMIN->value)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Role')
                    ->formatStateUsing(static function (string $state): string {
                        $role = RoleType::tryFrom($state);

                        return $role ? $role->getLabel() : $state;
                    }),
                TextColumn::make('tenant_name')
                    ->label('Tenant')
                    ->formatStateUsing(static function ($state, Role $record): string {
                        if (empty($record->team_id) || (int) $record->team_id === 0) {
                            return 'Global';
                        }

                        return (string) ($state ?: '—');
                    })
                    ->badge()
                    ->color('primary'),
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
