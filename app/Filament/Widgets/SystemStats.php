<?php

namespace App\Filament\Widgets;

use App\Enums\RoleType;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Tenants\TenantResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Video;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * @property-read array $tenantsData
 * @property-read array $usersData
 * @property-read array $mediaData
 * @property-read array $summary
 */
class SystemStats extends BaseWidget
{
    #[Computed]
    protected function tenantsData(): array
    {
        return Cache::store('redis')->remember('stats:tenants', 60, function (): array {
            $totalTenants = Tenant::query()->count();

            $approvedTenants = Tenant::query()
                ->whereHas('users', function (\Illuminate\Contracts\Database\Query\Builder $query): void {
                    $query->whereHas('roles', function (\Illuminate\Contracts\Database\Query\Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count();

            $activeTenants = Tenant::query()
                ->where('is_active', true)
                ->whereHas('users', function (\Illuminate\Contracts\Database\Query\Builder $query): void {
                    $query->whereHas('roles', function (\Illuminate\Contracts\Database\Query\Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count();

            $inactiveTenants = Tenant::query()
                ->where('is_active', false)
                ->whereHas('users', function (\Illuminate\Contracts\Database\Query\Builder $query): void {
                    $query->whereHas('roles', function (\Illuminate\Contracts\Database\Query\Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count();

            $unapprovedTenants = Tenant::query()
                ->whereDoesntHave('users', function (\Illuminate\Contracts\Database\Query\Builder $query): void {
                    $query->whereHas('roles', function (\Illuminate\Contracts\Database\Query\Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count();

            return [
                'total' => $totalTenants,
                'approved' => $approvedTenants,
                'active' => $activeTenants,
                'inactive' => $inactiveTenants,
                'unapproved' => $unapprovedTenants,
            ];
        });
    }

    #[Computed]
    protected function usersData(): array
    {
        return Cache::store('redis')->remember('stats:users', 60, function (): array {
            $baseQuery = User::query()
                ->whereDoesntHave('roles', fn ($q) => $q->where('name', RoleType::ADMIN->value));

            $totalUsers = $baseQuery->count();

            $activeUsers = (clone $baseQuery)
                ->where('is_suspended', false)
                ->where('is_approved', true)
                ->count();

            $suspendedUsers = (clone $baseQuery)
                ->where('is_suspended', true)
                ->count();

            $unapprovedUsers = (clone $baseQuery)
                ->where('is_suspended', false)
                ->where('is_approved', false)
                ->count();

            return [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
                'unapproved' => $unapprovedUsers,
            ];
        });
    }

    #[Computed]
    protected function mediaData(): array
    {
        return Cache::store('redis')->remember('stats:media', 60, function (): array {
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

            return [
                'total' => $totalMedia,
                'size' => $this->humanSize($totalSizeBytes),
            ];
        });
    }

    #[Computed]
    protected function summary(): array
    {
        return [
            'tenants' => $this->tenantsData,
            'users' => $this->usersData,
            'media' => $this->mediaData,
        ];
    }

    #[\Override]
    protected function getStats(): array
    {
        $s = $this->summary;

        return [
            // Widget Tenants
            Stat::make('Tenants', number_format($s['tenants']['total']))
                ->description(
                    'Aprovados: '.number_format($s['tenants']['approved']).' | '.
                    'Ativos: '.number_format($s['tenants']['active']).' | '.
                    'Inativos: '.number_format($s['tenants']['inactive']).' | '.
                    'Não Aprovados: '.number_format($s['tenants']['unapproved'])
                )
                ->icon('heroicon-c-building-office')
                ->url(TenantResource::getUrl()),

            // Widget Usuários
            Stat::make('Usuários', number_format($s['users']['total']))
                ->description(
                    'Ativos: '.number_format($s['users']['active']).' | '.
                    'Suspensos: '.number_format($s['users']['suspended']).' | '.
                    'Não Aprovados: '.number_format($s['users']['unapproved'])
                )
                ->icon('heroicon-c-user-group')
                ->url(UserResource::getUrl()),

            // Widget Mídia
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
