<?php

namespace App\Filament\Resources\Media\Widgets;

use App\Models\Video;
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
        // Vídeos são contabilizados pela tabela "videos" (fontes externas), não pelo Spatie
        $videos = Video::query()->count();
        $audios = SpatieMedia::query()->where('mime_type', 'like', 'audio/%')->count();
        $documents = SpatieMedia::query()->where('mime_type', 'like', 'application/%')->count();

        // Espaço total deve desconsiderar vídeos (somente anexos do Spatie que não são vídeo)
        $totalSizeBytes = (int) SpatieMedia::query()
            ->where('mime_type', 'not like', 'video/%')
            ->sum('size');
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
        $stats = $this->summary;

        // Calcular total de arquivos
        $totalFiles = $stats['images'] + $stats['videos'] + $stats['audios'] + $stats['documents'];

        // Calcular percentuais
        $imagesPercentage = $totalFiles > 0
            ? round(($stats['images'] / $totalFiles) * 100, 1)
            : 0;

        $videosPercentage = $totalFiles > 0
            ? round(($stats['videos'] / $totalFiles) * 100, 1)
            : 0;

        $audiosPercentage = $totalFiles > 0
            ? round(($stats['audios'] / $totalFiles) * 100, 1)
            : 0;

        $documentsPercentage = $totalFiles > 0
            ? round(($stats['documents'] / $totalFiles) * 100, 1)
            : 0;

        return [
            Stat::make('Imagens', number_format($stats['images']))
                ->description("{$imagesPercentage}% do total")
                ->icon('heroicon-c-photo')
                ->color('primary'),

            Stat::make('Vídeos', number_format($stats['videos']))
                ->description("{$videosPercentage}% do total")
                ->icon('heroicon-c-video-camera')
                ->color('warning'),

            Stat::make('Documentos', number_format($stats['documents']))
                ->description("{$documentsPercentage}% do total")
                ->icon('heroicon-c-document')
                ->color('success'),

            Stat::make('Áudios', number_format($stats['audios']))
                ->description("{$audiosPercentage}% do total")
                ->icon('heroicon-c-musical-note')
                ->color('danger'),

            Stat::make('Tamanho total', $stats['size'])
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
        $gb = $bytes / (1024 * 1024 * 1024);
        $gbRounded = round($gb, 2);
        if ($gbRounded > 0) {
            return $gbRounded.' GB';
        }

        $mb = $bytes / (1024 * 1024);
        $mbRounded = round($mb, 2);
        if ($mbRounded > 0) {
            return $mbRounded.' MB';
        }

        $kb = $bytes / 1024;
        $kbRounded = round($kb, 2);
        if ($kbRounded > 0) {
            return $kbRounded.' KB';
        }

        return $bytes.' B';
    }
}
