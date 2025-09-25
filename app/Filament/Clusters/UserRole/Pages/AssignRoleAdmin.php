<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\TenantUser;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleAdmin extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected string $view = 'filament.clusters.user-role.pages.assign-role-admin';

    protected static ?string $title = 'Definir Administradores';

    protected static ?string $navigationLabel = 'Administradores';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_admin')
                ->label('Administrador')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(static function (TenantUser $record): bool {
                    return $record->user->hasRole(RoleType::ADMIN->value);
                })
                ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                    if ($state) {
                        $record->user->syncRoles([RoleType::ADMIN->value]);
                    } else {
                        $record->user->removeRole(RoleType::ADMIN->value);
                    }
                }),
        ];
    }
}
