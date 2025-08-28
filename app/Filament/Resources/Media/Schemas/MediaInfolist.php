<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Infolists\Components\AudioPreviewEntry;
use App\Filament\Infolists\Components\DocumentPreviewEntry;
use App\Filament\Infolists\Components\ImagePreviewEntry;
use App\Filament\Infolists\Components\VideoPreviewEntry;
use App\Models\Media;
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
                Section::make('Conteúdo')
                    ->components([
                        ImagePreviewEntry::make('image')
                            ->hiddenLabel()
                            ->hidden(fn (Media $r) => ! $r->is_image)
                            ->columnSpanFull(),

                        AudioPreviewEntry::make('audio_url')
                            ->hiddenLabel()
                            ->state(fn (Media $r) => $r->is_audio && $r->disk === 'public' ? asset('storage/'.$r->file_name) : null)
                            ->hidden(fn (Media $r) => ! $r->is_audio)
                            ->columnSpanFull(),

                        VideoPreviewEntry::make('youtube_embed')
                            ->hiddenLabel()
                            ->state(fn (Media $r) => filled($r->custom_properties['youtube_id'] ?? null)
                                ? 'https://www.youtube.com/embed/'.$r->custom_properties['youtube_id']
                                : null)
                            ->hidden(fn (Media $r) => ! $r->is_video)
                            ->columnSpanFull(),

                        DocumentPreviewEntry::make('doc_url')
                            ->hiddenLabel()
                            ->state(fn (Media $r) => $r->is_document && $r->disk === 'public' ? asset('storage/'.$r->file_name) : null)
                            ->hidden(fn (Media $r) => ! $r->is_document)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
