<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\TenantUser;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleOwner extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected string $view = 'filament.clusters.user-role.pages.assign-role-owner';

    protected static ?string $title = 'Definir Proprietários';

    protected static ?string $navigationLabel = 'Proprietários';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_owner')
                ->label('Proprietário')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(static function (TenantUser $record): bool {
                    return $record->user->roles()
                        ->where('name', RoleType::OWNER->value)
                        ->where('roles.team_id', $record->tenant_id)
                        ->exists();
                })
                ->disabled(fn (TenantUser $record): bool => $record->user->roles()
                    ->where('name', RoleType::USER->value)
                    ->where('roles.team_id', $record->tenant_id)
                    ->exists()
                )
                ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                    if ($state) {
                        // Remove role User no tenant específico
                        $record->user->removeAllUserRolesFromTenant($record->tenant);

                        // Atribui role Owner no tenant específico
                        $roleOwner = RoleType::ensureOwnerRoleForTeam($record->tenant_id, 'web');
                        $record->user->assignRoleInTenant($roleOwner, $record->tenant);
                    } else {
                        $record->user->removeRoleFromTenant(RoleType::OWNER->value, $record->tenant);
                    }
                }),
        ];
    }
}
