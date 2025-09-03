<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Filament\Infolists\Components\AudioPreviewEntry;
use App\Filament\Infolists\Components\DocumentPreviewEntry;
use App\Filament\Infolists\Components\ImagePreviewEntry;
use App\Filament\Infolists\Components\VideoPreviewEntry;
use App\Models\MediaItem;
use Filament\Schemas\Components\Section;

class MediaPreviewSection
{
    public static function make(): Section
    {
        return Section::make(function ($record) {
            if (! $record) {
                return 'Mídia';
            }

            $attachment = $record->getFirstMedia('media');
            $mimeType = (string) ($attachment->mime_type ?? '');

            if (str_starts_with($mimeType, 'image/')) {
                return 'Imagem';
            }

            if (($record->video ?? false) || str_starts_with($mimeType, 'video/')) {
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
                    ->hidden(function (?MediaItem $record): bool {
                        if ($record === null) {
                            return true;
                        }
                        $m = $record->getFirstMedia('media');

                        return ! $m || ! str_starts_with((string) $m->mime_type, 'image/');
                    })
                    ->columnSpanFull(),

                AudioPreviewEntry::make('audio_url')
                    ->hiddenLabel()
                    ->state(function (?MediaItem $record): ?string {
                        if (! $record) {
                            return null;
                        }
                        $m = $record->getFirstMedia('media');
                        if (! $m || ! str_starts_with((string) $m->mime_type, 'audio/')) {
                            return null;
                        }
                        try {
                            return $m->getUrl();
                        } catch (\Throwable) {
                            return null;
                        }
                    })
                    ->hidden(function (?MediaItem $record): bool {
                        if ($record === null) {
                            return true;
                        }
                        $m = $record->getFirstMedia('media');

                        return ! $m || ! str_starts_with((string) $m->mime_type, 'audio/');
                    })
                    ->columnSpanFull(),

                VideoPreviewEntry::make('youtube_embed')
                    ->hiddenLabel()
                    ->state(function (?MediaItem $record): ?string {
                        return $record?->video?->url ?: null;
                    })
                    ->hidden(function (?MediaItem $record): bool {
                        if ($record === null) {
                            return true;
                        }

                        return ! ($record->video ?? false);
                    })
                    ->columnSpanFull(),

                DocumentPreviewEntry::make('doc_url')
                    ->hiddenLabel()
                    ->state(function (?MediaItem $record): ?string {
                        if (! $record) {
                            return null;
                        }
                        $m = $record->getFirstMedia('media');
                        if (! $m || ! str_starts_with((string) $m->mime_type, 'application/')) {
                            return null;
                        }
                        try {
                            return $m->getUrl();
                        } catch (\Throwable) {
                            return null;
                        }
                    })
                    ->hidden(function (?MediaItem $record): bool {
                        if ($record === null) {
                            return true;
                        }
                        $m = $record->getFirstMedia('media');

                        return ! $m || ! str_starts_with((string) $m->mime_type, 'application/');
                    })
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }
}
