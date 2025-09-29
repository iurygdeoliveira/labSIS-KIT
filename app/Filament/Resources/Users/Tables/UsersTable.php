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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    use NotificationsTrait;

    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->withoutRole(RoleType::ADMIN->value)
                    ->with('tenants')
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable(isIndividual: true, isGlobal: false)
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('tenants.name')
                    ->label('Tenants')
                    ->listWithLineBreaks()
                    ->bulleted(),
                TextColumn::make('tenant_roles')
                    ->label('Funções')
                    ->state(function (User $record): array|string {
                        if (method_exists($record, 'hasRole') && $record->hasRole(RoleType::ADMIN->value)) {
                            return '—';
                        }

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
                    })
                    ->listWithLineBreaks()
                    ->bulleted(),
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
}
