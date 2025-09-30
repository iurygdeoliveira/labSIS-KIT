<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Permissions;

use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class PermissionsCluster extends Cluster
{
    protected static ?string $title = '';

    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $navigationLabel = 'Permissões';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        if ($user->hasRole(RoleType::ADMIN->value)) {
            return true;
        }

        $currentTenant = Filament::getTenant();
        if ($currentTenant instanceof Tenant && $user->isOwnerOfTenant($currentTenant)) {
            return true;
        }

        return parent::canAccessClusteredComponents();
    }
}
