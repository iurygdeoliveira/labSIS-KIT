<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Infolists\Components\AudioPreviewEntry;
use App\Filament\Infolists\Components\DocumentPreviewEntry;
use App\Filament\Infolists\Components\ImagePreviewEntry;
use App\Filament\Infolists\Components\VideoPreviewEntry;
use App\Models\Media;
use Filament\Schemas\Components\Section;

class MediaPreviewSection
{
    public static function make(): Section
    {
        return Section::make(function ($record) {
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
            ->components([
                ImagePreviewEntry::make('image')
                    ->hiddenLabel()
                    ->hidden(fn (?Media $record): bool => $record === null || ! $record->is_image)
                    ->columnSpanFull(),

                AudioPreviewEntry::make('audio_url')
                    ->hiddenLabel()
                    ->state(fn (?Media $record): ?string => $record?->is_audio && $record?->disk === 'public' ? asset('storage/'.$record->file_name) : null)
                    ->hidden(fn (?Media $record): bool => $record === null || ! $record->is_audio)
                    ->columnSpanFull(),

                VideoPreviewEntry::make('youtube_embed')
                    ->hiddenLabel()
                    ->state(fn (?Media $record): ?string => filled($record?->custom_properties['youtube_id'] ?? null)
                        ? 'https://www.youtube.com/embed/'.$record->custom_properties['youtube_id']
                        : null)
                    ->hidden(fn (?Media $record): bool => $record === null || ! $record->is_video)
                    ->columnSpanFull(),

                DocumentPreviewEntry::make('doc_url')
                    ->hiddenLabel()
                    ->state(fn (?Media $record): ?string => $record?->is_document && $record?->disk === 'public' ? asset('storage/'.$record->file_name) : null)
                    ->hidden(fn (?Media $record): bool => $record === null || ! $record->is_document)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
