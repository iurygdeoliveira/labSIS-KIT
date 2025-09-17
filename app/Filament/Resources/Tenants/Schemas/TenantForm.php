<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')->label('Nome')->required()->maxLength(255),
            TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
            Toggle::make('is_active')->label('Ativo')->default(true),
            Repeater::make('users')
                ->relationship('users')
                ->schema([
                    Select::make('user_id')
                        ->relationship('users', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Toggle::make('is_owner')->label('Proprietário')->default(false),
                ])
                ->collapsed()
                ->grid(1),
        ]);
    }
}
