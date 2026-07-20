<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Organization\OrganizationResource;
use App\Filament\Resources\Users\UserResource;
use App\Support\FilamentStatsCache;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;

/**
 * @property-read array $teamsData
 * @property-read array $usersData
 * @property-read array $mediaData
 * @property-read array $summary
 */
class SystemStats extends BaseWidget
{
    protected ?string $pollingInterval = null;

    #[Computed]
    protected function teamsData(): array
    {
        return FilamentStatsCache::teams();
    }

    #[Computed]
    protected function usersData(): array
    {
        return FilamentStatsCache::users();
    }

    #[Computed]
    protected function mediaData(): array
    {
        $media = FilamentStatsCache::media();

        return [
            'total' => $media['total'],
            'size' => $media['size_human'],
        ];
    }

    #[Computed]
    protected function summary(): array
    {
        return [
            'teams' => $this->teamsData,
            'users' => $this->usersData,
            'media' => $this->mediaData,
        ];
    }

    #[\Override]
    protected function getStats(): array
    {
        $s = $this->summary;

        return [
            Stat::make('Organizações', number_format($s['teams']['total']))
                ->description(
                    'Aprovadas: '.number_format($s['teams']['approved']).' | '.
                    'Ativas: '.number_format($s['teams']['active']).' | '.
                    'Inativas: '.number_format($s['teams']['inactive']).' | '.
                    'Não Aprovadas: '.number_format($s['teams']['unapproved'])
                )
                ->icon(Heroicon::BuildingOffice)
                ->url(OrganizationResource::getUrl()),

            Stat::make('Usuários', number_format($s['users']['total']))
                ->description(
                    'Ativos: '.number_format($s['users']['active']).' | '.
                    'Suspensos: '.number_format($s['users']['suspended']).' | '.
                    'Não Aprovados: '.number_format($s['users']['unapproved'])
                )
                ->icon(Heroicon::UserGroup)
                ->url(UserResource::getUrl()),

            Stat::make('Mídias', number_format($s['media']['total']))
                ->description('Tamanho total: '.$s['media']['size'])
                ->url(MediaResource::getUrl()),
        ];
    }

    #[\Override]
    protected function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }

    #[\Override]
    public function getColumnSpan(): int|string|array
    {
        return [
            'sm' => 2,
            'lg' => 3,
            'xl' => 3,
        ];
    }
}
