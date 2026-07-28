<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Schemas;

use App\Enums\Changelog\ChangeType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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

                        Grid::make(2)
                            ->columnSpan(2)
                            ->schema([
                                Toggle::make('is_released')
                                    ->label('Versão Lançada?')
                                    ->live()
                                    ->default(false)
                                    ->columnSpan(1),

                                DatePicker::make('released_at')
                                    ->label('Data de Lançamento')
                                    ->native(false)
                                    ->visible(fn (Get $get): bool => (bool) $get('is_released'))
                                    ->columnSpan(1),
                            ]),
                    ]),

                Section::make('Detalhes da Alteração')
                    ->description('Classificação, descrição e ordenação do item no changelog')
                    ->columns(2)
                    ->components([
                        Select::make('type')
                            ->label('Tipo de Alteração')
                            ->options(ChangeType::class)
                            ->required()
                            ->searchable()
                            ->native(false)
                            ->default(ChangeType::Added)
                            ->columnSpan(2),

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
