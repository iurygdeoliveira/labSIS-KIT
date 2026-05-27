<?php

declare(strict_types=1);

use App\Models\User;
use App\Support\FilamentStatsCache;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

beforeEach(function (): void {
    /** @var TestCase $this */
    $this->seed(DatabaseSeeder::class);
    Cache::flush();
});

describe('FilamentStatsCache', function (): void {
    it('invalida contagens de usuários e tabs ao aprovar um usuário', function (): void {
        $pendingUser = User::factory()->create([
            'is_approved' => false,
        ]);

        $before = FilamentStatsCache::usersTabBadges();

        $pendingUser->update(['is_approved' => true]);

        $after = FilamentStatsCache::usersTabBadges();

        expect($after['approved'])->toBe($before['approved'] + 1)
            ->and($after['unapproved'])->toBe($before['unapproved'] - 1);
    });

    it('reutiliza cache de stats de usuários entre chamadas', function (): void {
        $first = FilamentStatsCache::users();
        $second = FilamentStatsCache::users();

        expect($first)->toBe($second)
            ->and(FilamentStatsCache::store()->has(FilamentStatsCache::KEY_USERS))->toBeTrue();
    });
});
