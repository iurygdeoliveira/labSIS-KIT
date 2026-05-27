<?php

namespace App\Filament\Resources\Media\Schemas;

use App\Support\AppDateTime;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
                            ->state(fn ($record): ?string => $record->linkedVideo()?->title)
                            ->visible(fn ($record): bool => (bool) $record->video),

                        TextEntry::make('human_size')
                            ->label('Tamanho')
                            ->visible(fn ($record): bool => ! (bool) $record->video),

                        TextEntry::make('video_duration')
                            ->label('Duração do Vídeo')
                            ->state(fn ($record): ?int => $record->linkedVideo()?->duration_seconds)
                            ->formatStateUsing(fn ($state): string => self::formatVideoDuration($state))
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
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'Imagem' => Heroicon::Photo,
                                'Vídeo' => Heroicon::VideoCamera,
                                'Documento' => Heroicon::Document,
                                'Áudio' => Heroicon::MusicalNote,
                                default => Heroicon::QuestionMarkCircle,
                            }),

                        TextEntry::make('created_at_display')
                            ->label('Data de Criação')
                            ->state(fn ($record): ?string => self::resolveCreatedAt($record)),
                    ])
                    ->columns(2),
            ]);
    }

    private static function resolveMediaActionConfig($record): array
    {
        if ((bool) $record->video) {
            $url = $record->linkedVideo()?->url;

            return [
                'label' => 'Assistir',
                'icon' => Heroicon::VideoCamera,
                'media' => $url ?: null,
            ];
        }

        $url = $record->getFirstMedia('media')?->getUrl();
        $fileType = (string) $record->file_type;

        $icon = match ($fileType) {
            'Imagem' => Heroicon::Photo,
            'Áudio' => Heroicon::MusicalNote,
            'Documento' => Heroicon::Document,
            default => Heroicon::Eye,
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

    private static function formatVideoDuration($state): string
    {
        if (! is_numeric($state)) {
            return '-';
        }

        $total = (int) $state;
        $hours = intdiv($total, 3600);
        $minutes = intdiv($total % 3600, 60);
        $seconds = $total % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    private static function resolveCreatedAt($record): ?string
    {
        if ((bool) $record->video) {
            $createdAt = $record->linkedVideo()?->created_at;

            return $createdAt ? AppDateTime::parse($createdAt)->format('d/m/Y H:i') : null;
        }

        $media = $record->getFirstMedia('media');

        return $media?->created_at?->format('d/m/Y H:i');
    }
}
