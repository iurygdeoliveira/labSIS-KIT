<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleType;
use App\Events\UserApproved;
use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $query = User::query()->withoutRole(RoleType::ADMIN->value);
        $currentTeam = Filament::getTenant();

        if (! $isAdmin) {
            if ($currentTeam instanceof Team) {
                $query->whereHas('teams', function ($q) use ($currentTeam): void {
                    $q->where('teams.id', $currentTeam->getKey());
                })->withRolesForTeam($currentTeam);
            }
        } else {
            $query->with(['teams', 'rolesWithTeams']);
        }

        return $table
            ->query($query)
            ->columns([
                self::getNameColumn(),
                self::getEmailColumn(),
                ...self::getTenantsColumn($isAdmin),
                self::getRolesColumn($isAdmin),
                self::getStatusColumn(),
                self::getApprovalColumn(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->icon(Heroicon::Eye)
                    ->tooltip('Visualizar')
                    ->color('secondary'),
                EditAction::make()
                    ->iconButton()
                    ->icon(Heroicon::Pencil)
                    ->tooltip('Editar')
                    ->visible(fn (User $record): bool => Filament::auth()->user()->can('update', $record) && $record->isApproved()),
                DeleteUserAction::make()->iconButton()->icon(Heroicon::Trash)->tooltip('Excluir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'desc');
    }

    /**
     * Coluna do nome do usuário
     */
    private static function getNameColumn(): Column
    {
        return TextColumn::make('name')
            ->searchable(isIndividual: true, isGlobal: false)
            ->sortable();
    }

    /**
     * Coluna do email do usuário
     */
    private static function getEmailColumn(): Column
    {
        return TextColumn::make('email')
            ->label('Email address');
    }

    /**
     * Coluna de teams (apenas para Admin)
     */
    private static function getTenantsColumn(bool $isAdmin): array
    {
        if (! $isAdmin) {
            return [];
        }

        return [
            TextColumn::make('teams.name')
                ->label('Teams')
                ->listWithLineBreaks(fn (?User $record): bool => $record instanceof User && $record->isApproved())
                ->bulleted(fn (?User $record): bool => $record instanceof User && $record->isApproved()),
        ];
    }

    /**
     * Coluna de funções/roles do usuário
     */
    private static function getRolesColumn(bool $isAdmin)
    {
        return TextColumn::make('tenant_roles')
            ->label($isAdmin ? 'Funções' : 'Função')
            ->state(fn (User $record): array|string => self::getTenantRolesForUser($record, $isAdmin))
            ->listWithLineBreaks(fn (?User $record): bool => $record instanceof User && $record->isApproved())
            ->when($isAdmin, fn ($column): TextColumn => $column->bulleted(fn (?User $record): bool => $record instanceof User && $record->isApproved()));
    }

    /**
     * Coluna de status (suspenso/autorizado) - apenas para usuários aprovados
     */
    private static function getStatusColumn(): Column
    {
        return TextColumn::make('is_suspended')
            ->label('Acesso')
            ->sortable()
            ->formatStateUsing(fn (User $record): string => $record->is_suspended ? __('Suspenso') : __('Liberado'))
            ->badge()
            ->color(fn (User $record): string => $record->is_suspended ? 'danger' : 'primary')
            ->alignCenter();
    }

    /**
     * Coluna de aprovação - apenas para usuários não aprovados e visível para Admin/Owner
     */
    private static function getApprovalColumn(): ToggleColumn
    {
        return ToggleColumn::make('is_approved')
            ->onColor('primary')
            ->offColor('danger')
            ->onIcon(Heroicon::Check)
            ->offIcon(Heroicon::XMark)
            ->label('Aprovar')
            ->afterStateUpdated(function (User $record, $state): void {
                // Se o usuário foi aprovado
                if ($state) {
                    // Remover suspensão
                    $record->is_suspended = false;

                    // Se o email não está verificado, verificar automaticamente
                    if (! $record->hasVerifiedEmail()) {
                        $record->markEmailAsVerified();
                    }

                    $record->save();

                    // Disparar evento de aprovação
                    event(new UserApproved($record));
                }
            });
    }

    private static function getTenantRolesForUser(User $record, bool $isAdmin): array|string
    {
        if ($record->hasRole(RoleType::ADMIN->value)) {
            return '—';
        }

        if ($isAdmin) {
            return self::getAdminViewRoles($record);
        }

        return self::getTenantViewRoles($record);
    }

    private static function getAdminViewRoles(User $record): array|string
    {
        $teams = $record->teams;

        if ($teams->isEmpty()) {
            return '—';
        }

        $lines = [];

        foreach ($teams as $team) {
            if (! $team instanceof Team) {
                continue;
            }

            $roles = $record->rolesWithTeams
                ->where('team_id', $team->id);

            if ($roles->isEmpty()) {
                $lines[] = '—';

                continue;
            }

            $labels = $roles->map(fn ($role): string => self::getRoleLabel($role->name))->all();

            $lines[] = implode(', ', $labels);
        }

        return $lines;
    }

    private static function getTenantViewRoles(User $record): string
    {
        $roles = $record->rolesWithTeams ?? collect();

        if ($roles->isEmpty()) {
            return '—';
        }

        $firstRole = $roles->first();

        return $firstRole ? self::getRoleLabel($firstRole->name) : '—';
    }

    private static function getRoleLabel(string $name): string
    {
        try {
            return RoleType::from($name)->getLabel();
        } catch (\ValueError) {
            return $name;
        }
    }
}
