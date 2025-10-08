<?php

namespace App\Filament\Pages\Auth;

use Filament\Facades\Filament;
use Filament\Pages\Concerns\HasRoutes;
use Filament\Pages\SimplePage;
use Illuminate\Database\Eloquent\Model;

class AccountSuspended extends SimplePage
{
    use HasRoutes;

    protected string $view = 'filament.pages.auth.account-suspended';

    protected static bool $shouldRegisterNavigation = false;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        $panelInstance = $panel ? Filament::getPanel($panel) : Filament::getCurrentOrDefaultPanel();

        if (blank($panel) || $panelInstance->hasTenancy()) {
            $parameters['tenant'] ??= ($tenant ?? Filament::getTenant());
        }

        return route(static::getRouteName($panel), $parameters, $isAbsolute);
    }

    public static function getRouteName(?string $panel = null): string
    {
        $panelInstance = $panel ? Filament::getPanel($panel) : Filament::getCurrentOrDefaultPanel();

        $routeName = static::getRelativeRouteName($panelInstance);

        return $panelInstance->generateRouteName($routeName);
    }

    public static function registerNavigationItems(): void {}
}
