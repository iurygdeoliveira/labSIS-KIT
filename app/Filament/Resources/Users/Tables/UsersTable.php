<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleType;
use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User && method_exists($currentUser, 'hasRole')) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $query = User::query()->withoutRole(RoleType::ADMIN->value);
        $currentTenant = Filament::getTenant();

        if (! $isAdmin) {
            if ($currentTenant) {
                $query->whereHas('tenants', function ($q) use ($currentTenant) {
                    $q->where('tenants.id', $currentTenant->id);
                })->withRolesForTenant($currentTenant);
            }
        } else {
            $query->with(['tenants', 'rolesWithTeams']);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address'),
                // Coluna de Tenants - apenas para Admin
                ...($isAdmin ? [
                    TextColumn::make('tenants.name')
                        ->label('Tenants')
                        ->listWithLineBreaks()
                        ->bulleted(),
                ] : []),
                // Coluna de Funções - adaptada ao contexto
                TextColumn::make('tenant_roles')
                    ->label($isAdmin ? 'Funções' : 'Função')
                    ->state(fn (User $record): array|string => self::getTenantRolesForUser($record, $isAdmin))
                    ->listWithLineBreaks()
                    ->when($isAdmin, fn ($column) => $column->bulleted()),
                TextColumn::make('is_suspended')
                    ->label('Status')
                    ->sortable()
                    ->formatStateUsing(fn (User $record): string => $record->is_suspended ? __('Suspenso') : __('Autorizado'))
                    ->badge()
                    ->color(fn (User $record): string => $record->is_suspended ? 'danger' : 'primary')
                    ->icon(fn (User $record): string => $record->is_suspended ? 'heroicon-c-no-symbol' : 'heroicon-c-check')
                    ->alignCenter(),
            ])
            ->filters([
                //
            ])

            ->recordActions([
                ViewAction::make()->icon('heroicon-s-eye')->label('')->tooltip('Visualizar'),
                EditAction::make()->icon('heroicon-s-pencil')->label('')->tooltip('Editar'),
                DeleteUserAction::make()->icon('heroicon-s-trash')->label('')->tooltip('Excluir'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'desc');
    }

    private static function getTenantRolesForUser(User $record, bool $isAdmin): array|string
    {
        if (method_exists($record, 'hasRole') && $record->hasRole(RoleType::ADMIN->value)) {
            return '—';
        }

        if ($isAdmin) {
            return self::getAdminViewRoles($record);
        }

        return self::getTenantViewRoles($record);
    }

    private static function getAdminViewRoles(User $record): array|string
    {
        $tenants = $record->tenants;

        if ($tenants->isEmpty()) {
            return '—';
        }

        $lines = [];

        foreach ($tenants as $tenant) {
            $roles = $record->rolesWithTeams
                ->where('team_id', $tenant->id);

            if ($roles->isEmpty()) {
                $lines[] = '—';

                continue;
            }

            $labels = $roles->map(fn ($role) => self::getRoleLabel($role->name))->all();

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
