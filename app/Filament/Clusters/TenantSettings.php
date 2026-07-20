<?php

namespace App\Filament\Clusters;

use App\Enums\RoleType;
use App\Models\Organization;
use App\Models\User;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class TenantSettings extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 9999;

    public static function getNavigationLabel(): string
    {
        return __('organization.settings.navigation_label');
    }

    public static function getNavigationGroup(): ?string
    {
        $user = Filament::auth()->user();

        if ($user?->hasRole(RoleType::ADMIN->value)) {
            return 'Sistema';
        }

        return 'Administração';
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $currentTeam = Filament::getTenant();

        return $currentTeam instanceof Organization && $user->isOwnerOfOrganization($currentTeam);
    }
}
