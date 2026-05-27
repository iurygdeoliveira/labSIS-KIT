<?php

namespace App\Filament\Resources\Media\Widgets;

use App\Support\FilamentStatsCache;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

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
        $stats = FilamentStatsCache::media();

        return [
            'images' => $stats['images'],
            'videos' => $stats['videos'],
            'audios' => $stats['audios'],
            'documents' => $stats['documents'],
            'size' => $stats['size_human'],
        ];
    }

    #[Computed]
    protected function percentages(): array
    {
        $stats = $this->summary;
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
}
