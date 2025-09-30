<?php

namespace App\Filament\Resources\Users\Tables;

use App\Enums\RoleType;
use App\Filament\Resources\Users\Actions\DeleteUserAction;
use App\Models\Role;
use App\Models\User;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    use NotificationsTrait;

    public static function configure(Table $table): Table
    {
        $currentUser = Filament::auth()->user();
        $isAdmin = false;

        if ($currentUser instanceof User && method_exists($currentUser, 'hasRole')) {
            $isAdmin = $currentUser->hasRole(RoleType::ADMIN->value);
        }

        $query = User::query()->withoutRole(RoleType::ADMIN->value);

        // Se não for admin, filtra apenas usuários do tenant atual
        if (! $isAdmin) {
            $currentTenant = Filament::getTenant();
            if ($currentTenant) {
                $query->whereHas('tenants', function ($q) use ($currentTenant) {
                    $q->where('tenants.id', $currentTenant->id);
                });
            }
        } else {
            // Admin vê todos os usuários com seus tenants
            $query->with('tenants');
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

        // Se for Admin, mostra todas as roles de todos os tenants
        if ($isAdmin) {
            $tenants = $record->tenants;

            if ($tenants->isEmpty()) {
                return '—';
            }

            $lines = [];

            foreach ($tenants as $tenant) {
                $roleNames = Role::query()
                    ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
                    ->where('mhr.model_type', User::class)
                    ->where('mhr.model_id', $record->id)
                    ->where('mhr.team_id', $tenant->id)
                    ->pluck('roles.name')
                    ->all();

                if (empty($roleNames)) {
                    $lines[] = '—';

                    continue;
                }

                $labels = array_map(static function (string $name): string {
                    try {
                        return RoleType::from($name)->getLabel();
                    } catch (\ValueError) {
                        return $name;
                    }
                }, $roleNames);

                $lines[] = implode(', ', $labels);
            }

            return $lines;
        }

        // Se não for Admin, mostra apenas a role do tenant atual
        $currentTenant = Filament::getTenant();
        if (! $currentTenant) {
            return '—';
        }

        $roleName = Role::query()
            ->join('model_has_roles as mhr', 'mhr.role_id', '=', 'roles.id')
            ->where('mhr.model_type', User::class)
            ->where('mhr.model_id', $record->id)
            ->where('mhr.team_id', $currentTenant->id)
            ->value('roles.name');

        if (! $roleName) {
            return '—';
        }

        try {
            return RoleType::from($roleName)->getLabel();
        } catch (\ValueError) {
            return $roleName;
        }
    }

    private static function getRoleColor(string $state): string
    {
        return match ($state) {
            'Administrador' => 'danger',
            'Proprietário' => 'warning',
            'Usuário' => 'primary',
            default => 'gray'
        };
    }
}
