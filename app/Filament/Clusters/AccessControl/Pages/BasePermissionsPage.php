<?php

declare(strict_types=1);

namespace App\Filament\Clusters\AccessControl\Pages;

use App\Enums\Permission;
use App\Enums\RoleType;
use App\Filament\Clusters\AccessControl\AccessControlCluster;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

abstract class BasePermissionsPage extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $cluster = AccessControlCluster::class;

    protected static ?string $title = 'Controle de Permissões';

    abstract public function getResourceName(): string;

    public function table(Table $table): Table
    {
        return $table
            ->query(Role::query()->where('name', '!=', RoleType::ADMIN->value))
            ->columns([
                TextColumn::make('name')
                    ->label('Role'),
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
