<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Enums\RoleType;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nome do Tenant')
                ->description('Informe o nome do tenant')
                ->components([
                    TextInput::make('name')
                        ->hiddenLabel()
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label('Ativo')
                        ->visible(fn ($record): bool => $record !== null),
                ])
                ->columns(1),

            Section::make('Usuários')
                ->description('Associe usuários a este tenant')
                ->components([
                    Select::make('usersIds')
                        ->hiddenLabel()
                        ->options(fn (): array => User::query()
                            ->whereDoesntHave('roles', function ($query): void {
                                $query->where('name', RoleType::ADMIN->value);
                            })
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->afterStateHydrated(function (Set $set, $state, $record): void {
                            if ($record === null) {
                                return;
                            }

                            $set('usersIds', $record->users()->pluck('users.id')->all());
                        }),
                ])
                ->columns(1),
        ]);
    }
}
