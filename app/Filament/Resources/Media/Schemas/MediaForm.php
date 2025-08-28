<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Arquivo')
                    ->description('Dados básicos do arquivo de mídia')
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
                        TextInput::make('mime_type')
                            ->label('Tipo de Arquivo')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('size')
                            ->label('Tamanho')
                            ->required()
                            ->suffix(fn ($record) => $record ? $record->human_size : ''),
                    ])
                    ->columns(2),

                Section::make(function ($record) {
                    if (! $record) {
                        return 'Mídia';
                    }

                    $mimeType = $record->mime_type;

                    if (str_starts_with($mimeType, 'image/')) {
                        return 'Imagem';
                    }

                    if (str_starts_with($mimeType, 'video/')) {
                        return 'Vídeo';
                    }

                    if (str_starts_with($mimeType, 'audio/')) {
                        return 'Áudio';
                    }

                    if (str_starts_with($mimeType, 'application/')) {
                        return 'Documento';
                    }

                    return 'Preview da Mídia';
                })
                    ->description('Visualização do conteúdo')
                    ->components([
                        View::make('filament.components.media-preview')
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
