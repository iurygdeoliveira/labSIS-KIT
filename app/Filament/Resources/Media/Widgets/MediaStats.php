<?php

namespace App\Filament\Resources\Media\Widgets;

use App\Models\Video;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @property-read array $summary
 * @property-read array $percentages
 */
class MediaStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[Computed]
    protected function summary(): array
    {
        $images = SpatieMedia::query()
            ->where('mime_type', 'like', 'image/%')
            ->where('collection_name', '!=', 'avatar')
            ->count();
        // Vídeos são contabilizados pela tabela "videos" (fontes externas), não pelo Spatie
        $videos = Video::query()->count();
        $audios = SpatieMedia::query()->where('mime_type', 'like', 'audio/%')->count();
        $documents = SpatieMedia::query()->where('mime_type', 'like', 'application/%')->count();

        // Espaço total deve desconsiderar vídeos (somente anexos do Spatie que não são vídeo)
        $totalSizeBytes = (int) SpatieMedia::query()
            ->where('mime_type', 'not like', 'video/%')
            ->where('collection_name', '!=', 'avatar')
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

    #[Computed]
    protected function percentages(): array
    {
        $stats = $this->summary;

        // Calcular total de arquivos
        $totalFiles = $stats['images'] + $stats['videos'] + $stats['audios'] + $stats['documents'];

        if ($totalFiles === 0) {
            return [
                'images' => 0,
                'videos' => 0,
                'audios' => 0,
                'documents' => 0,
            ];
        }

        return [
            'images' => round(($stats['images'] / $totalFiles) * 100, 1),
            'videos' => round(($stats['videos'] / $totalFiles) * 100, 1),
            'audios' => round(($stats['audios'] / $totalFiles) * 100, 1),
            'documents' => round(($stats['documents'] / $totalFiles) * 100, 1),
        ];
    }

    #[\Override]
    protected function getStats(): array
    {
        $stats = $this->summary;
        $percentages = $this->percentages;

        return [
            Stat::make('Imagens', number_format($stats['images']))
                ->description("{$percentages['images']}% do total")
                ->icon(Heroicon::Photo)
                ->color('primary'),

            Stat::make('Vídeos', number_format($stats['videos']))
                ->description("{$percentages['videos']}% do total")
                ->icon(Heroicon::VideoCamera)
                ->color('warning'),

            Stat::make('Documentos', number_format($stats['documents']))
                ->description("{$percentages['documents']}% do total")
                ->icon(Heroicon::Document)
                ->color('info'),

            Stat::make('Áudios', number_format($stats['audios']))
                ->description("{$percentages['audios']}% do total")
                ->icon(Heroicon::MusicalNote)
                ->color('danger'),

            Stat::make('Tamanho total', $stats['size'])
                ->description('Espaço utilizado')
                ->icon(Heroicon::ServerStack)
                ->color('secondary'),
        ];
    }

    #[\Override]
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
