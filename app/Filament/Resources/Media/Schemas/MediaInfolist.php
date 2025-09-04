<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Hugomyb\FilamentMediaAction\Actions\MediaAction;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Arquivo')
                    ->components([
                        TextEntry::make('name')
                            ->label('Nome do Arquivo')
                            ->visible(fn ($record): bool => ! (bool) $record->video),

                        // Evita colisão entre atributo booleano 'video' e relacionamento 'video()'
                        TextEntry::make('video_title')
                            ->label('Titulo do Video')
                            ->state(fn ($record): ?string => $record->video()->value('title'))
                            ->visible(fn ($record): bool => (bool) $record->video),

                        TextEntry::make('human_size')
                            ->label('Tamanho')
                            ->visible(fn ($record): bool => ! (bool) $record->video),

                        TextEntry::make('video_duration')
                            ->label('Duração do Vídeo')
                            ->state(fn ($record): ?int => $record->video()->value('duration_seconds'))
                            ->formatStateUsing(function ($state): string {
                                if (! is_numeric($state)) {
                                    return '-';
                                }

                                $total = (int) $state;
                                $hours = intdiv($total, 3600);
                                $minutes = intdiv($total % 3600, 60);
                                $seconds = $total % 60;

                                return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                            })
                            ->visible(fn ($record): bool => (bool) $record->video),

                        MediaAction::make('open')
                            ->label(fn ($record) => self::resolveMediaActionConfig($record)['label'])
                            ->icon(fn ($record) => self::resolveMediaActionConfig($record)['icon'])
                            ->media(fn ($record) => self::resolveMediaActionConfig($record)['media'] ?? '#')
                            ->visible(fn ($record): bool => self::resolveMediaActionConfig($record)['media'] !== null),
                    ])
                    ->columns(3),

                Section::make('Detalhes')
                    ->components([
                        TextEntry::make('file_type')
                            ->label('Tipo de Arquivo')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Imagem' => 'primary',
                                'Vídeo' => 'warning',
                                'Documento' => 'success',
                                'Áudio' => 'danger',
                                default => 'secondary',
                            })
                            ->icon(fn (string $state): string => match ($state) {
                                'Imagem' => 'heroicon-c-photo',
                                'Vídeo' => 'heroicon-c-video-camera',
                                'Documento' => 'heroicon-c-document',
                                'Áudio' => 'heroicon-c-musical-note',
                            }),

                        TextEntry::make('created_at')
                            ->label('Data de Criação')
                            ->dateTime('d/m/Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    private static function resolveMediaActionConfig($record): array
    {
        if ((bool) $record->video) {
            $url = $record->video()->value('url');

            return [
                'label' => 'Assistir',
                'icon' => 'heroicon-s-video-camera',
                'media' => $url ?: null,
            ];
        }

        $url = $record->getFirstMedia('media')?->getUrl();
        $fileType = (string) $record->file_type;

        $icon = match ($fileType) {
            'Imagem' => 'heroicon-s-photo',
            'Áudio' => 'heroicon-s-musical-note',
            'Documento' => 'heroicon-s-document',
            default => 'heroicon-s-eye',
        };

        $label = match ($fileType) {
            'Áudio' => 'Ouvir',
            default => 'Abrir',
        };

        return [
            'label' => $label,
            'icon' => $icon,
            'media' => $url ?: null,
        ];
    }
}
