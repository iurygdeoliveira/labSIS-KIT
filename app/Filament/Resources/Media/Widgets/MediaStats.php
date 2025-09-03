<?php

namespace App\Filament\Resources\Media\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class MediaStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        $images = SpatieMedia::query()->where('mime_type', 'like', 'image/%')->count();
        $videos = SpatieMedia::query()->where('mime_type', 'like', 'video/%')->count();
        $audios = SpatieMedia::query()->where('mime_type', 'like', 'audio/%')->count();
        $documents = SpatieMedia::query()->where('mime_type', 'like', 'application/%')->count();

        $totalSizeBytes = (int) SpatieMedia::query()->sum('size');
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

        // Calcular total de arquivos
        $totalFiles = $s['images'] + $s['videos'] + $s['audios'] + $s['documents'];

        // Calcular percentuais
        $imagesPercentage = $totalFiles > 0
            ? round(($s['images'] / $totalFiles) * 100, 1)
            : 0;

        $videosPercentage = $totalFiles > 0
            ? round(($s['videos'] / $totalFiles) * 100, 1)
            : 0;

        $audiosPercentage = $totalFiles > 0
            ? round(($s['audios'] / $totalFiles) * 100, 1)
            : 0;

        $documentsPercentage = $totalFiles > 0
            ? round(($s['documents'] / $totalFiles) * 100, 1)
            : 0;

        return [
            Stat::make('Imagens', number_format($s['images']))
                ->description("{$imagesPercentage}% do total")
                ->icon('heroicon-c-photo')
                ->color('primary'),

            Stat::make('Vídeos', number_format($s['videos']))
                ->description("{$videosPercentage}% do total")
                ->icon('heroicon-c-video-camera')
                ->color('warning'),

            Stat::make('Documentos', number_format($s['documents']))
                ->description("{$documentsPercentage}% do total")
                ->icon('heroicon-c-document')
                ->color('success'),

            Stat::make('Áudios', number_format($s['audios']))
                ->description("{$audiosPercentage}% do total")
                ->icon('heroicon-c-musical-note')
                ->color('danger'),

            Stat::make('Tamanho total', $s['size'])
                ->description('Espaço utilizado')
                ->icon('heroicon-c-server-stack')
                ->color('secondary'),
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
