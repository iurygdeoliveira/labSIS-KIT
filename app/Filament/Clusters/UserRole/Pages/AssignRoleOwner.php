<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\TenantUser;
use Filament\Facades\Filament;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleOwner extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'icon-admin';

    protected string $view = 'filament.clusters.user-role.pages.assign-role-owner';

    protected static ?string $title = 'Definir Proprietários';

    protected static ?string $navigationLabel = 'Usuários Proprietários';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_owner')
                ->label('Proprietário')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(static fn (TenantUser $record): bool => $record->user->isOwnerOfTenant($record->tenant))
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                    if ($state) {
                        // Remove TODAS as outras roles do usuário no tenant específico
                        $record->user->removeAllUserRolesFromTenant($record->tenant);

                        // Remove qualquer outra role que possa existir
                        $allRoles = $record->user->getRolesForTenant($record->tenant);
                        foreach ($allRoles as $role) {
                            if ($role->name !== RoleType::OWNER->value) {
                                $record->user->removeRoleFromTenant($role->name, $record->tenant);
                            }
                        }

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
