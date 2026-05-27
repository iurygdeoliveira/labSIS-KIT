<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\RoleType;
use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use App\Models\Video;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

final class FilamentStatsCache
{
    public const int TTL_SECONDS = 60;

    public const string KEY_TEAMS = 'stats:teams';

    public const string KEY_USERS = 'stats:users';

    public const string KEY_MEDIA = 'stats:media';

    public const string KEY_USERS_TABS = 'stats:users:tabs';

    public static function store(): Repository
    {
        return Cache::store((string) config('cache.default'));
    }

    /**
     * @return array{total: int, approved: int, active: int, inactive: int, unapproved: int, active_flag: int, inactive_flag: int, total_members: int, avg_users_per_team: float}
     */
    public static function teams(): array
    {
        return self::store()->remember(self::KEY_TEAMS, self::TTL_SECONDS, function (): array {
            $totalTeams = Team::query()->count('*');

            $approvedTeams = Team::query()
                ->whereHas('members', function (Builder $query): void {
                    $query->whereHas('roles', function (Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count('*');

            $activeTeams = Team::query()
                ->where('is_active', true)
                ->whereHas('members', function (Builder $query): void {
                    $query->whereHas('roles', function (Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count('*');

            $inactiveTeams = Team::query()
                ->where('is_active', false)
                ->whereHas('members', function (Builder $query): void {
                    $query->whereHas('roles', function (Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count('*');

            $unapprovedTeams = Team::query()
                ->whereDoesntHave('members', function (Builder $query): void {
                    $query->whereHas('roles', function (Builder $roleQuery): void {
                        $roleQuery->where('name', RoleType::OWNER->value);
                    })
                        ->where('is_approved', true);
                })
                ->count('*');

            $activeFlagCount = Team::query()->where('is_active', true)->count('*');
            $totalMembers = Membership::query()->count('*');

            return [
                'total' => $totalTeams,
                'approved' => $approvedTeams,
                'active' => $activeTeams,
                'inactive' => $inactiveTeams,
                'unapproved' => $unapprovedTeams,
                'active_flag' => $activeFlagCount,
                'inactive_flag' => max($totalTeams - $activeFlagCount, 0),
                'total_members' => $totalMembers,
                'avg_users_per_team' => $totalTeams > 0
                    ? round($totalMembers / $totalTeams, 1)
                    : 0.0,
            ];
        });
    }

    /**
     * @return array{total: int, active: int, suspended: int, unapproved: int, verified: int}
     */
    public static function users(): array
    {
        return self::store()->remember(self::KEY_USERS, self::TTL_SECONDS, function (): array {
            $baseQuery = User::query()
                ->whereDoesntHave('roles', fn ($q) => $q->where('name', RoleType::ADMIN->value));

            $totalUsers = $baseQuery->count('*');

            $activeUsers = (clone $baseQuery)
                ->where('is_suspended', false)
                ->where('is_approved', true)
                ->count('*');

            $suspendedUsers = (clone $baseQuery)
                ->where('is_suspended', true)
                ->count('*');

            $unapprovedUsers = (clone $baseQuery)
                ->where('is_suspended', false)
                ->where('is_approved', false)
                ->count('*');

            $verifiedUsers = (clone $baseQuery)
                ->whereNotNull('email_verified_at')
                ->count('*');

            return [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'suspended' => $suspendedUsers,
                'unapproved' => $unapprovedUsers,
                'verified' => $verifiedUsers,
            ];
        });
    }

    /**
     * @return array{approved: int, unapproved: int}
     */
    public static function usersTabBadges(): array
    {
        return self::store()->remember(self::KEY_USERS_TABS, self::TTL_SECONDS, function (): array {
            $baseQuery = User::query()->withoutRole(RoleType::ADMIN->value);

            return [
                'approved' => (clone $baseQuery)->where('is_approved', true)->count('*'),
                'unapproved' => (clone $baseQuery)->where('is_approved', false)->count('*'),
            ];
        });
    }

    /**
     * @return array{images: int, videos: int, audios: int, documents: int, total: int, size_bytes: int, size_human: string}
     */
    public static function media(): array
    {
        return self::store()->remember(self::KEY_MEDIA, self::TTL_SECONDS, function (): array {
            $images = SpatieMedia::query()
                ->where('mime_type', 'like', 'image/%')
                ->where('collection_name', '!=', 'avatar')
                ->count('*');
            $videos = Video::query()->count('*');
            $audios = SpatieMedia::query()->where('mime_type', 'like', 'audio/%')->count('*');
            $documents = SpatieMedia::query()->where('mime_type', 'like', 'application/%')->count('*');
            $totalMedia = $images + $videos + $audios + $documents;

            $totalSizeBytes = (int) SpatieMedia::query()
                ->where('mime_type', 'not like', 'video/%')
                ->where('collection_name', '!=', 'avatar')
                ->sum('size');

            return [
                'images' => $images,
                'videos' => $videos,
                'audios' => $audios,
                'documents' => $documents,
                'total' => $totalMedia,
                'size_bytes' => $totalSizeBytes,
                'size_human' => self::humanSize($totalSizeBytes),
            ];
        });
    }

    public static function forgetUsers(): void
    {
        self::store()->forget(self::KEY_USERS);
        self::store()->forget(self::KEY_USERS_TABS);
    }

    public static function forgetTeams(): void
    {
        self::store()->forget(self::KEY_TEAMS);
    }

    public static function forgetMedia(): void
    {
        self::store()->forget(self::KEY_MEDIA);
    }

    public static function humanSize(int $bytes): string
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
