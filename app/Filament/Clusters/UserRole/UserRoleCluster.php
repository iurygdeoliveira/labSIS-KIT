<?php

declare(strict_types=1);

namespace App\Filament\Clusters\UserRole;

use App\Enums\RoleType;
use App\Models\Tenant;
use App\Models\User;
use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use UnitEnum;

class UserRoleCluster extends Cluster
{
    protected static string|BackedEnum|null $navigationIcon = 'icon-role';

    protected static ?string $navigationLabel = 'Funções';

    protected static string|UnitEnum|null $navigationGroup = 'Administração';

    protected static ?string $title = '';

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
