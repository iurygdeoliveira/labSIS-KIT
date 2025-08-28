<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Arquivo')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome do Arquivo'),
                        TextEntry::make('file_type')
                            ->label('Tipo de Arquivo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Imagem' => 'primary',
                                'Vídeo' => 'warning',
                                'Documento' => 'success',
                                'Áudio' => 'danger'
                            }),
                        TextEntry::make('human_size')
                            ->label('Tamanho'),
                        TextEntry::make('created_at')
                            ->label('Data de Criação')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),

                MediaPreviewSection::make(),
            ]);
    }
}
