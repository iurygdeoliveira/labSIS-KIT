<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole\Pages;

use App\Enums\RoleType;
use App\Models\TenantUser;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ToggleColumn;

class AssignRoleUser extends BaseAssignRolePage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::User;

    protected string $view = 'filament.clusters.user-role.pages.assign-role-user';

    protected static ?string $title = 'Definir Usuários Comuns';

    protected static ?string $navigationLabel = 'Usuários Comuns';

    protected function getExtraColumns(): array
    {
        return [
            ToggleColumn::make('role_user')
                ->label('Usuário comum')
                ->onColor('primary')
                ->offColor('danger')
                ->onIcon('heroicon-c-check')
                ->offIcon('heroicon-c-x-mark')
                ->getStateUsing(static fn (TenantUser $record): bool => $record->user->isUserOfTenant($record->tenant))
                ->disabled(fn (TenantUser $record): bool => $record->user_id === Filament::auth()->id())
                ->tooltip(fn (TenantUser $record): ?string => $record->user_id === Filament::auth()->id()
                        ? 'Você não pode alterar a própria role'
                        : null
                )
                ->updateStateUsing(static function (TenantUser $record, bool $state): void {
                    if ($state) {
                        // Remove role Owner no tenant específico se existir
                        $record->user->removeAllOwnerRolesFromTenant($record->tenant);

                        // Remove qualquer outra role que possa existir (exceto User)
                        $allRoles = $record->user->getRolesForTenant($record->tenant);
                        foreach ($allRoles as $role) {
                            if ($role->name !== RoleType::USER->value) {
                                $record->user->removeRoleFromTenant($role->name, $record->tenant);
                            }
                        }

                        // Atribui role User no tenant específico
                        $roleUser = RoleType::ensureUserRoleForTeam($record->tenant_id, 'web');
                        $record->user->assignRoleInTenant($roleUser, $record->tenant);
                    } else {
                        $record->user->removeRoleFromTenant(RoleType::USER->value, $record->tenant);
                    }
                }),
        ];
    }
}
