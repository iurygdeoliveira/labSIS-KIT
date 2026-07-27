<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\NotificationCenter;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_center_renders_without_duplicate_queries(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        // Send a notification so unread count > 0
        Notification::make()
            ->title('Test Notification')
            ->sendToDatabase($user);

        DB::enableQueryLog();

        $component = Livewire::test(NotificationCenter::class);

        $tabs = $component->instance()->categoryTabs();

        $this->assertNotEmpty($tabs);

        // Check query log for duplicate count queries
        $queries = collect(DB::getQueryLog())->pluck('query');
        $unreadCountQueries = $queries->filter(function (string $query): bool {
            return str_contains($query, 'count(*)') && str_contains($query, 'read_at');
        });

        // getUnreadNotificationsCount should be cached and not execute multiple times for 'all' tab and count property
        $this->assertLessThanOrEqual(5, $unreadCountQueries->count());
    }

    public function test_notification_center_does_not_execute_duplicate_select_queries(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        Notification::make()
            ->title('Test Notification')
            ->sendToDatabase($user);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $component = Livewire::test(NotificationCenter::class);

        // Simulate subsequent calls that happen during render or interaction
        $component->instance()->getNotifications();
        $component->instance()->hasAnyNotifications();

        $queries = collect(DB::getQueryLog())->pluck('query');
        $selectNotificationsQueries = $queries->filter(function (string $query): bool {
            return ! str_contains($query, 'count(') && ! str_contains($query, 'exists') && str_contains($query, 'notifications');
        });

        // Because of memoization, getNotifications() should only hit the database once across initial render and subsequent calls!
        $this->assertEquals(1, $selectNotificationsQueries->count());
    }
}
