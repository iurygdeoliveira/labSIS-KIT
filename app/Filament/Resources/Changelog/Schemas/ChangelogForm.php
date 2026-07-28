<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Schemas;


use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

use Filament\Schemas\Schema;

class ChangelogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Versão')
                    ->description('Identificação da versão do sistema e status de lançamento')
                    ->columns(2)
                    ->components([
                        TextInput::make('version')
                            ->label('Versão')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('ex: 1.2.0 ou Unreleased')
                            ->columnSpan(2),

                        DatePicker::make('released_at')
                            ->label('Data de Lançamento')
                            ->native(false)
                            ->columnSpan(2),
                    ]),

                Section::make('Detalhes da Alteração')
                    ->description('Classificação, descrição e ordenação do item no changelog')
                    ->columns(2)
                    ->components([

                        Textarea::make('description')
                            ->label('Descrição')
                            ->required()
                            ->rows(4)
                            ->placeholder('Descreva a alteração realizada...')
                            ->columnSpan(2),

                        TextInput::make('sort')
                            ->label('Ordem de Exibição')
                            ->numeric()
                            ->default(0)
                            ->helperText('Menor número aparece primeiro no grupo da versão.')
                            ->columnSpan(2),
                    ]),
            ]);
    }
}
