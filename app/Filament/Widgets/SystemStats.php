<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Tenants\TenantResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Computed;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class SystemStats extends BaseWidget
{
    #[Computed]
    protected function summary(): array
    {
        // Tenants
        $totalTenants = Tenant::query()->count();
        $activeTenants = Tenant::query()->where('is_active', true)->count();
        $inactiveTenants = max($totalTenants - $activeTenants, 0);
        $activeTenantsPct = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0;
        $inactiveTenantsPct = $totalTenants > 0 ? round(($inactiveTenants / $totalTenants) * 100, 1) : 0;

        // Usuários
        $totalUsers = User::query()->count();
        $suspendedUsers = User::query()->where('is_suspended', true)->count();
        $activeUsers = max($totalUsers - $suspendedUsers, 0);
        $activeUsersPct = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0;
        $suspendedUsersPct = $totalUsers > 0 ? round(($suspendedUsers / $totalUsers) * 100, 1) : 0;

        // Mídia (Spatie) + Vídeos (externos)
        $images = SpatieMedia::query()
            ->where('mime_type', 'like', 'image/%')
            ->where('collection_name', '!=', 'avatar')
            ->count();
        $videos = Video::query()->count();
        $audios = SpatieMedia::query()->where('mime_type', 'like', 'audio/%')->count();
        $documents = SpatieMedia::query()->where('mime_type', 'like', 'application/%')->count();
        $totalMedia = $images + $videos + $audios + $documents;

        $totalSizeBytes = (int) SpatieMedia::query()
            ->where('mime_type', 'not like', 'video/%')
            ->where('collection_name', '!=', 'avatar')
            ->sum('size');
        $totalSizeHuman = $this->humanSize($totalSizeBytes);

        return [
            'tenants' => [
                'total' => $totalTenants,
                'active' => $activeTenants,
                'inactive' => $inactiveTenants,
                'activePct' => $activeTenantsPct,
                'inactivePct' => $inactiveTenantsPct,
            ],
            'users' => [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
                'activePct' => $activeUsersPct,
                'suspendedPct' => $suspendedUsersPct,
            ],
            'media' => [
                'total' => $totalMedia,
                'size' => $totalSizeHuman,
            ],
        ];
    }

    protected function getStats(): array
    {
        $s = $this->summary;

        return [
            // Tenants
            Stat::make('Tenants Ativos', number_format($s['tenants']['active']))
                ->description($s['tenants']['activePct'].'% do total')
                ->icon('heroicon-c-check-badge')
                ->color('primary')
                ->url(TenantResource::getUrl()),

            Stat::make('Tenants Inativos', number_format($s['tenants']['inactive']))
                ->description($s['tenants']['inactivePct'].'% do total')
                ->icon('heroicon-c-no-symbol')
                ->color('danger')
                ->url(TenantResource::getUrl()),

            // Usuários
            Stat::make('Usuários Ativos', number_format($s['users']['active']))
                ->description($s['users']['activePct'].'% do total')
                ->icon('heroicon-c-user-group')
                ->color('primary')
                ->url(UserResource::getUrl()),

            Stat::make('Usuários Suspensos', number_format($s['users']['suspended']))
                ->description($s['users']['suspendedPct'].'% do total')
                ->icon('heroicon-c-no-symbol')
                ->color('danger')
                ->url(UserResource::getUrl()),

            // Mídia
            Stat::make('Mídias', number_format($s['media']['total']))
                ->description('Tamanho total: '.$s['media']['size'])
                ->icon('heroicon-c-photo')
                ->color('success')
                ->url(MediaResource::getUrl()),
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

    public function getColumnSpan(): int|string|array
    {
        return [
            'sm' => 2,
            'lg' => 3,
            'xl' => 3,
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
