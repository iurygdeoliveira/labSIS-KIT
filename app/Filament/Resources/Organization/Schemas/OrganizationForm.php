<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informações Gerais')
                ->description('Dados cadastrais da organização')
                ->columns(2)
                ->components([
                    TextInput::make('name')
                        ->label(__('organization.fields.name'))
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    TextInput::make('slug')
                        ->label(__('organization.fields.slug'))
                        ->required()
                        ->unique(ignorable: fn ($record) => $record)
                        ->maxLength(255)
                        ->columnSpan(1),
                ]),
        ]);
    }
}
