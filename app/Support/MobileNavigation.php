<?php

declare(strict_types=1);

namespace App\Support;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationGroup;

class MobileNavigation
{
    /**
     * Get the navigation items for the mobile bottom nav.
     *
     * @return array<MobileNavigationItem>
     */
    public static function getItems(int $limit = 4): array
    {
        if (! Filament::auth()->check()) {
            return [];
        }

        if (Filament::hasTenancy() && ! Filament::getTenant()) {
            return [];
        }

        /** @var array<NavigationGroup> $groups */
        $groups = Filament::getNavigation();

        $allItems = [];

        foreach ($groups as $group) {
            $groupIcon = $group->getIcon();

            foreach ($group->getItems() as $navItem) {
                if ($navItem->isHidden()) {
                    continue;
                }

                $icon = $navItem->getIcon() ?? $groupIcon;

                if ($icon === null) {
                    continue;
                }

                $activeIcon = $navItem->getActiveIcon();

                $allItems[] = MobileNavigationItem::make(
                    label: $navItem->getLabel(),
                    url: $navItem->getUrl() ?? '#',
                    icon: $icon,
                    activeIcon: $activeIcon,
                    isActive: $navItem->isActive(),
                    badge: $navItem->getBadge(),
                    badgeColor: $navItem->getBadgeColor(),
                    sort: $navItem->getSort() ?? 0
                );
            }
        }

        // Sort items by their Filament sort order
        usort($allItems, fn (MobileNavigationItem $a, MobileNavigationItem $b): int => $a->getSort() <=> $b->getSort());

        return array_slice($allItems, 0, $limit);
    }
}
