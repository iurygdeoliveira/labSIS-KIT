<?php

declare(strict_types=1);

namespace App\Traits\Filament;

use App\Filament\Configurators\NavigationSortConfig;

trait HasConfigurableNavigationSort
{
    public static function getNavigationSort(): ?int
    {
        return NavigationSortConfig::getSortOrder(static::class) ?? parent::getNavigationSort();
    }
}
