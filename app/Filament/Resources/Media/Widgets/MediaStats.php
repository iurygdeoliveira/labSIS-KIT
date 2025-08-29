<?php

namespace App\Filament\Resources\Media\Widgets;

use App\Models\Media;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

class MediaStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        $images = Media::query()->where('mime_type', 'like', 'image/%')->count();
        $videos = Media::query()->where('mime_type', 'like', 'video/%')->count();
        $audios = Media::query()->where('mime_type', 'like', 'audio/%')->count();
        $documents = Media::query()->where('mime_type', 'like', 'application/%')->count();

        $totalSizeBytes = (int) Media::query()->sum('size');
        $totalSizeHuman = $this->humanSize($totalSizeBytes);

        return [
            'images' => $images,
            'videos' => $videos,
            'audios' => $audios,
            'documents' => $documents,
            'size' => $totalSizeHuman,
        ];
    }

    protected function getStats(): array
    {
        $s = $this->summary;

        return [
            Stat::make('Imagens', (string) $s['images'])
                ->icon('heroicon-c-photo'),
            Stat::make('Vídeos', (string) $s['videos'])
                ->icon('heroicon-c-video-camera'),
            Stat::make('Documentos', (string) $s['documents'])
                ->icon('heroicon-c-document'),
            Stat::make('Áudios', (string) $s['audios'])
                ->icon('heroicon-c-musical-note'),
            Stat::make('Tamanho total', (string) $s['size'])
                ->icon('heroicon-c-server-stack'),
        ];
    }

    protected function getColumns(): int|array
    {
        return [
            'sm' => 2,
            'md' => 3,
            'xl' => 5,
        ];
    }

    private function humanSize(int $bytes): string
    {
        $gigabytes = $bytes / (1024 * 1024 * 1024);

        return round($gigabytes, 2).' GB';
    }
}
