<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Arquivo')
                    ->components([
                        TextInput::make('name')
                            ->label('Nome do Arquivo')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('file_name')
                            ->label('Nome do Arquivo no Sistema')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),

                MediaPreviewSection::make(),

            ]);
    }
}
