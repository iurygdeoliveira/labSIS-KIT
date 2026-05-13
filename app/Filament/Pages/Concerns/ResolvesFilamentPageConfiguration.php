<?php

namespace App\Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Pages\PageConfiguration;
use Filament\Panel;

/**
 * Needed when extending SimplePage with HasRoutes — Filament 5.6+ calls getConfiguration()
 * from HasRoutes::getSlug().
 */
trait ResolvesFilamentPageConfiguration
{
    public static function getConfiguration(?Panel $panel = null): ?PageConfiguration
    {
        $key = Filament::getCurrentPageConfigurationKey();

        if ($key === null) {
            return null;
        }

        $panel ??= Filament::getCurrentPanel();

        return $panel->getPageConfiguration(static::class, $key);
    }
}
