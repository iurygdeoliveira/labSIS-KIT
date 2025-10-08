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
use Livewire\Attributes\Computed;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class SystemStats extends BaseWidget
{
    #[Computed]
    protected function tenantsData(): array
    {
        $totalTenants = Tenant::query()->count();

        // Tenants ativos: is_active = true E owner aprovado
        $activeTenants = Tenant::query()
            ->where('is_active', true)
            ->whereHas('users', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', RoleType::OWNER->value);
                })
                    ->whereNotNull('approved_at');
            })
            ->count();

        // Tenants inativos: is_active = false
        $inactiveTenants = Tenant::query()->where('is_active', false)->count();

        // Tenants não aprovados: is_active = true mas owner NÃO aprovado
        $unapprovedTenants = Tenant::query()
            ->where('is_active', true)
            ->whereDoesntHave('users', function ($query) {
                $query->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', RoleType::OWNER->value);
                })
                    ->whereNotNull('approved_at');
            })
            ->count();

        return [
            'total' => $totalTenants,
            'active' => $activeTenants,
            'inactive' => $inactiveTenants,
            'unapproved' => $unapprovedTenants,
        ];
    }

    #[Computed]
    protected function usersData(): array
    {
        $baseQuery = User::query()
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', RoleType::ADMIN->value));

        $totalUsers = $baseQuery->count();

        // Usuários ativos: não suspensos E aprovados
        $activeUsers = (clone $baseQuery)
            ->where('is_suspended', false)
            ->whereNotNull('approved_at')
            ->count();

        // Usuários suspensos
        $suspendedUsers = (clone $baseQuery)
            ->where('is_suspended', true)
            ->count();

        // Usuários não aprovados: approved_at null e não suspensos
        $unapprovedUsers = (clone $baseQuery)
            ->where('is_suspended', false)
            ->whereNull('approved_at')
            ->count();

        return [
            'total' => $totalUsers,
            'active' => $activeUsers,
            'suspended' => $suspendedUsers,
            'unapproved' => $unapprovedUsers,
        ];
    }

    #[Computed]
    protected function mediaData(): array
    {
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

        return [
            'total' => $totalMedia,
            'size' => $this->humanSize($totalSizeBytes),
        ];
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

    protected function getStats(): array
    {
        $s = $this->summary;

        return [
            // Widget Tenants
            Stat::make('Tenants', number_format($s['tenants']['total']))
                ->description(
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

    protected function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 3,
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
