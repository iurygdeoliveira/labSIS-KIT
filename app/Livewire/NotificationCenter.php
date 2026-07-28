<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Filament\Pages\ChangelogPage;
use App\Models\Changelog;
use App\Support\NotificationCenter\NotificationCategory;
use App\Support\NotificationCenter\NotificationCenterManager;
use App\Support\NotificationCenter\NotificationTab;
use Filament\Livewire\DatabaseNotifications;
use Filament\Actions\Action as NotificationAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\Computed;
use Override;

/**
 * @property-read Collection<int, NotificationTab> $categoryTabs
 */
class NotificationCenter extends DatabaseNotifications
{
    public string $activeCategory = 'all';

    protected ?int $unreadCountCache = null;

    protected ?bool $hasAnyNotificationsCache = null;

    protected DatabaseNotificationCollection|Paginator|null $notificationsCache = null;

    protected bool $changelogSyncedThisRequest = false;

    public function setActiveCategory(string $categoryId): void
    {
        $this->activeCategory = $categoryId;
        $this->clearNotificationCaches();

        $this->resetPage('database-notifications-page');
    }

    #[Override]
    public function getNotifications(): DatabaseNotificationCollection|Paginator
    {
        $this->syncUnreadChangelogNotifications();

        return $this->notificationsCache ??= parent::getNotifications();
    }

    #[Override]
    public function markAllNotificationsAsRead(): void
    {
        $this->clearNotificationCaches();
        parent::markAllNotificationsAsRead();

        $user = filament()->auth()->user() ?? Filament::auth()->user();
        if ($user && method_exists($user, 'markChangelogAsRead')) {
            $user->markChangelogAsRead();
        }
    }

    #[Override]
    public function clearNotifications(): void
    {
        $this->clearNotificationCaches();
        parent::clearNotifications();
    }

    protected function clearNotificationCaches(): void
    {
        $this->notificationsCache = null;
        $this->hasAnyNotificationsCache = null;
        $this->unreadCountCache = null;
    }

    #[Override]
    public function getUnreadNotificationsCount(): int
    {
        $this->syncUnreadChangelogNotifications();

        return $this->unreadCountCache ??= $this->getBaseNotificationsQuery()->whereNull('read_at')->count();
    }

    #[Override]
    public function getNotificationsQuery(): Builder|Relation
    {
        $query = $this->getBaseNotificationsQuery();

        if ($this->activeCategory === 'all') {
            return $query;
        }

        return $this->scopeQueryToCategory($query, $this->activeCategory);
    }

    /**
     * @return Collection<int, NotificationTab>
     */
    #[Computed]
    public function categoryTabs(): Collection
    {
        $panelId = filament()->getId();
        $categories = NotificationCenterManager::getCategoriesForPanel($panelId)->values();
        $defaultCategoryId = NotificationCenterManager::getDefaultCategory();

        if (! NotificationCenterManager::getCategoriesForPanel($panelId)->has($defaultCategoryId)) {
            $categories->push(NotificationCategory::make($defaultCategoryId)->label('Geral'));
        }

        $totalUnread = $this->getUnreadNotificationsCount();

        $tabs = collect([
            new NotificationTab(
                id: 'all',
                label: 'Todas',
                icon: null,
                color: null,
                count: $totalUnread,
            ),
        ]);

        foreach ($categories as $category) {
            $count = 0;

            if ($totalUnread > 0) {
                $count = $this->scopeQueryToCategory($this->getBaseNotificationsQuery(), $category->getId())
                    ->whereNull('read_at')
                    ->count();
            }

            $tabs->push(new NotificationTab(
                id: $category->getId(),
                label: $category->getLabel(),
                icon: $category->getIcon(),
                color: $category->getColor(),
                count: $count,
            ));
        }

        return $tabs;
    }

    protected function getBaseNotificationsQuery(): Builder|Relation
    {
        return parent::getNotificationsQuery();
    }

    #[Override]
    public function markAllNotificationsAsReadAction(): NotificationAction
    {
        return NotificationAction::make('markAllNotificationsAsRead')
            ->button()
            ->color('success')
            ->size('sm')
            ->label(__('filament-notifications::database.modal.actions.mark_all_as_read.label'))
            ->action('markAllNotificationsAsRead');
    }

    #[Override]
    public function clearNotificationsAction(): NotificationAction
    {
        return NotificationAction::make('clearNotifications')
            ->button()
            ->color('danger')
            ->size('sm')
            ->label(__('filament-notifications::database.modal.actions.clear.label'))
            ->action('clearNotifications')
            ->close();
    }

    #[Override]
    public function getNotification(DatabaseNotification $notification): Notification
    {
        $notif = parent::getNotification($notification);

        $category = $notification->data['viewData']['category'] ?? $notification->data['category'] ?? null;
        if ($category === 'system' || isset($notification->data['viewData']['changelog_version'])) {
            $notif->color('info');
            $notif->iconColor('info');

            foreach ($notif->getActions() as $action) {
                if ($action instanceof NotificationAction) {
                    $action->color('info');
                }
            }
        }

        return $notif;
    }

    public function getNotificationCategory(DatabaseNotification $notification): ?NotificationCategory
    {
        $rawCategory = $notification->data['viewData']['category'] ?? $notification->data['category'] ?? null;
        $catId = NotificationCenterManager::resolveCategoryId($rawCategory);
        $panelId = Filament::getId();
        $cat = NotificationCenterManager::getCategory($panelId, $catId);

        if (! $cat && $catId === NotificationCenterManager::getDefaultCategory()) {
            $cat = NotificationCategory::make($catId)->label('Geral');
        }

        return $cat;
    }

    protected function scopeQueryToCategory(Builder|Relation $query, string $categoryId): Builder|Relation
    {
        if ($categoryId === NotificationCenterManager::getDefaultCategory()) {
            return $query->where(function (Builder|Relation $query) use ($categoryId): void {
                $query->whereNull('data->viewData->category')
                    ->orWhere('data->viewData->category', $categoryId);
            });
        }

        return $query->where('data->viewData->category', $categoryId);
    }

    public function hasAnyNotifications(): bool
    {
        $this->syncUnreadChangelogNotifications();

        if ($this->notificationsCache !== null && $this->notificationsCache->isNotEmpty() && $this->activeCategory === 'all') {
            return true;
        }

        if ($this->getNotifications()->isNotEmpty() && $this->activeCategory === 'all') {
            return true;
        }

        return $this->hasAnyNotificationsCache ??= $this->getBaseNotificationsQuery()->exists();
    }

    /**
     * @return array{heading: string, description: string}
     */
    public function getCategoryEmptyState(string $categoryId): array
    {
        return NotificationCenterManager::getEmptyState($categoryId);
    }

    public function render(): View
    {
        return view('livewire.notification-center');
    }

    protected function syncUnreadChangelogNotifications(): void
    {
        if ($this->changelogSyncedThisRequest) {
            return;
        }
        $this->changelogSyncedThisRequest = true;

        $user = filament()->auth()->user() ?? Filament::auth()->user();
        if (! $user || ! method_exists($user, 'hasUnreadChangelog') || ! $user->hasUnreadChangelog()) {
            return;
        }

        $latestEntry = Changelog::query()
            ->where('is_released', true)
            ->whereNotNull('released_at')
            ->latest('released_at')
            ->first()
            ?? Changelog::query()->latest('created_at')->first()
            ?? Changelog::query()->latest('id')->first();

        if (! $latestEntry && file_exists(base_path('CHANGELOG.md'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('changelog:sync-github');
                $latestEntry = Changelog::query()
                    ->where('is_released', true)
                    ->whereNotNull('released_at')
                    ->latest('released_at')
                    ->first()
                    ?? Changelog::query()->latest('created_at')->first()
                    ?? Changelog::query()->latest('id')->first();
            } catch (\Throwable $e) {
                // Ignora erros de sincronização se estiver sem internet
            }
        }

        if (! $latestEntry) {
            return;
        }

        $alreadyNotified = $user->notifications()
            ->where(function (Builder|Relation $query) use ($latestEntry): void {
                $query->where('data->viewData->changelog_version', $latestEntry->version)
                    ->orWhere('data->title', 'like', '%' . $latestEntry->version . '%');
            })
            ->exists();

        if (! $alreadyNotified) {
            $url = '/';
            try {
                $url = ChangelogPage::getUrl();
            } catch (\Throwable $e) {
                // Fallback se não houver painel do filament no contexto
            }

            $notification = Notification::make()
                ->title('Nova atualização disponível: v' . $latestEntry->version)
                ->body('Uma nova versão do labSIS-KIT foi publicada no dia ' . $latestEntry->released_at->format('d/m/Y') . '. Confira as melhorias e novidades!')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->category('system')
                ->actions([
                    NotificationAction::make('view')
                        ->label('Ver Atualizações')
                        ->url($url)
                        ->color('info')
                        ->markAsRead(),
                ])
                ->viewData(['changelog_version' => $latestEntry->version, 'category' => 'system']);

            $user->notifyNow($notification->toDatabase());

            $this->clearNotificationCaches();
        }
    }
}
