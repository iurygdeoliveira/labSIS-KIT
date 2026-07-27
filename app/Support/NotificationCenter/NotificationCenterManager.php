<?php

declare(strict_types=1);

namespace App\Support\NotificationCenter;

use BackedEnum;
use Closure;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class NotificationCenterManager
{
    /**
     * @var array<string, Collection<string, NotificationCategory>>
     */
    protected static array $panelCategories = [];

    protected static string $defaultCategory = 'general';

    protected static ?Closure $emptyStateUsing = null;

    public static function boot(): void
    {
        if (! Notification::hasMacro('category')) {
            Notification::macro('category', function (string|BackedEnum|null $category): Notification {
                /** @var Notification $this */
                /** @phpstan-ignore variable.undefined, varTag.variableNotFound */
                $this->viewData(['category' => $category instanceof BackedEnum ? $category->value : $category]);

                /** @phpstan-ignore variable.undefined */
                return $this;
            });
        }

        if (! Notification::hasMacro('getCategory')) {
            Notification::macro('getCategory', function (): ?string {
                /** @var Notification $this */
                /** @phpstan-ignore variable.undefined, varTag.variableNotFound */
                return $this->getViewData()['category'] ?? null;
            });
        }
    }

    /**
     * @param  array<NotificationCategory | BackedEnum>  $categories
     */
    public static function registerCategories(string $panelId, array $categories): void
    {
        $collection = self::$panelCategories[$panelId] ?? collect();

        foreach ($categories as $category) {
            $cat = $category instanceof BackedEnum
                ? NotificationCategory::fromEnum($category)
                : $category;

            $collection->put($cat->getId(), $cat);
        }

        self::$panelCategories[$panelId] = $collection;
    }

    /**
     * @return Collection<string, NotificationCategory>
     */
    public static function getCategoriesForPanel(?string $panelId = null): Collection
    {
        $categories = self::$panelCategories[$panelId ?? 'default'] ?? collect();

        return $categories
            ->sortBy(fn (NotificationCategory $category): int => $category->getOrder())
            ->values()
            ->keyBy(fn (NotificationCategory $category): string => $category->getId());
    }

    public static function getCategory(?string $panelId, string $id): ?NotificationCategory
    {
        return self::getCategoriesForPanel($panelId)->get($id);
    }

    public static function setDefaultCategory(string $id): void
    {
        self::$defaultCategory = $id;
    }

    public static function getDefaultCategory(): string
    {
        return self::$defaultCategory;
    }

    public static function resolveCategoryId(?string $rawCategory): string
    {
        return filled($rawCategory) ? $rawCategory : self::getDefaultCategory();
    }

    /**
     * @param  Closure(string $categoryId): array{heading: string, description: string}  $callback
     */
    public static function emptyStateUsing(Closure $callback): void
    {
        self::$emptyStateUsing = $callback;
    }

    /**
     * @return array{heading: string, description: string}
     */
    public static function getEmptyState(string $categoryId): array
    {
        if (self::$emptyStateUsing) {
            return (self::$emptyStateUsing)($categoryId);
        }

        return [
            'heading' => __('filament-notifications::database.modal.empty.heading'),
            'description' => __('filament-notifications::database.modal.empty.description'),
        ];
    }

    public static function clearCategories(?string $panelId = null): void
    {
        if ($panelId === null) {
            self::$panelCategories = [];
        } else {
            unset(self::$panelCategories[$panelId]);
        }
    }
}
