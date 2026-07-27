<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\NotificationCenter\NotificationCategory;
use App\Support\NotificationCenter\NotificationCenterManager;
use App\Support\NotificationCenter\NotificationTab;
use Filament\Livewire\DatabaseNotifications;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Collection;
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

    public function setActiveCategory(string $categoryId): void
    {
        $this->activeCategory = $categoryId;
        $this->clearNotificationCaches();

        $this->resetPage('database-notifications-page');
    }

    #[Override]
    public function getNotifications(): DatabaseNotificationCollection|Paginator
    {
        return $this->notificationsCache ??= parent::getNotifications();
    }

    #[Override]
    public function markAllNotificationsAsRead(): void
    {
        $this->clearNotificationCaches();
        parent::markAllNotificationsAsRead();
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
}
