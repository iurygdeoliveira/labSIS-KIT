<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\Changelog\ChangeType;
use App\Enums\OrganizationRole;
use App\Filament\Resources\Changelog\ChangelogResource;
use App\Livewire\NotificationCenter;
use App\Models\Changelog;
use App\Models\User;
use App\Support\Changelog\KeepAChangelogParser;
use App\Support\Changelog\VersionGrouper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChangelogTest extends TestCase
{
    use RefreshDatabase;

    public function test_keep_a_changelog_parser_parses_portuguese_markdown(): void
    {
        $markdown = <<<'MD'
# Changelog

## [1.2.0] - 2026-07-27

### Adicionado
- Implementação nativa do changelog (por Iury em 27/07/2026 14:00).
- Badge de notificação na barra lateral.

### Modificado
- Otimização de performance.
MD;

        $parser = new KeepAChangelogParser;
        $entries = $parser->parse($markdown);

        $this->assertCount(3, $entries);
        $this->assertEquals('1.2.0', $entries[0]['version']);
        $this->assertTrue($entries[0]['is_released']);
        $this->assertEquals('2026-07-27', $entries[0]['released_at']);
        $this->assertEquals(ChangeType::Added, $entries[0]['type']);
        $this->assertStringContainsString('Implementação nativa do changelog', $entries[0]['description']);

        $this->assertEquals(ChangeType::Changed, $entries[2]['type']);
        $this->assertStringContainsString('Otimização de performance', $entries[2]['description']);
    }

    public function test_version_grouper_groups_and_sorts_entries(): void
    {
        $entries = collect([
            new Changelog([
                'version' => '1.0.0',
                'type' => ChangeType::Added,
                'description' => 'Lancamento 1.0.0',
                'is_released' => true,
                'sort' => 0,
            ]),
            new Changelog([
                'version' => '1.1.0',
                'type' => ChangeType::Fixed,
                'description' => 'Correcao em 1.1.0',
                'is_released' => true,
                'sort' => 0,
            ]),
            new Changelog([
                'version' => '1.1.0',
                'type' => ChangeType::Added,
                'description' => 'Novidade em 1.1.0',
                'is_released' => true,
                'sort' => 1,
            ]),
        ]);

        $grouper = new VersionGrouper;
        $grouped = $grouper->group($entries);

        $this->assertCount(2, $grouped);
        $this->assertTrue($grouped->has('1.1.0'));
        $this->assertTrue($grouped->has('1.0.0'));
        $this->assertEquals(['1.1.0', '1.0.0'], $grouped->keys()->toArray());
    }

    public function test_changelog_sync_command_populates_database(): void
    {
        $this->assertEquals(0, Changelog::count());

        Artisan::call('changelog:sync-github', ['--fresh' => true]);

        $this->assertGreaterThan(0, Changelog::count());

        $entry = Changelog::query()->where('version', '1.2.0')->first();
        $this->assertNotNull($entry);
        $this->assertTrue($entry->is_released);
    }

    public function test_user_unread_changelog_notification(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'last_read_changelog_at' => null,
        ]);

        Changelog::create([
            'version' => '2.0.0',
            'type' => ChangeType::Added,
            'description' => 'Nova grande atualização',
            'is_released' => true,
            'released_at' => now(),
            'sort' => 0,
        ]);

        $this->actingAs($user);

        $this->assertTrue($user->hasUnreadChangelog());
        $this->assertEquals(0, $user->notifications()->count());

        Livewire::test(NotificationCenter::class)
            ->call('getNotifications');

        $this->assertEquals(1, $user->notifications()->count());
        $this->assertEquals('info', $user->notifications()->first()->data['color']);

        $user->markChangelogAsRead();
        $user->unreadNotifications()->update(['read_at' => now()]);

        $this->assertFalse($user->fresh()->hasUnreadChangelog());
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }

    public function test_admin_can_manage_changelog_and_common_user_cannot(): void
    {
        Role::firstOrCreate(['name' => OrganizationRole::Admin->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => OrganizationRole::User->value, 'guard_name' => 'web']);

        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole(OrganizationRole::Admin->value);

        /** @var User $commonUser */
        $commonUser = User::factory()->create();
        $commonUser->assignRole(OrganizationRole::User->value);

        $this->actingAs($admin);
        $this->assertTrue(ChangelogResource::canViewAny());
        $this->assertFalse(ChangelogResource::shouldRegisterNavigation());

        $this->actingAs($commonUser);
        $this->assertFalse(ChangelogResource::canViewAny());
        $this->assertFalse(ChangelogResource::shouldRegisterNavigation());
    }
}
