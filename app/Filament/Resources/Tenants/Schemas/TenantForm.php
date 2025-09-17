<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                ])
                ->columns(1),

            Section::make('Usuários')
                ->description('Associe usuários a este tenant')
                ->components([
                    Select::make('usersIds')
                        ->hiddenLabel()
                        ->options(User::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ])
                ->columns(1),
        ]);
    }
}
