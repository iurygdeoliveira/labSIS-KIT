<?php

declare(strict_types=1);

namespace App\Tenancy;

use Filament\Facades\Filament;
use Spatie\Permission\Contracts\PermissionsTeamResolver;

class SpatieTeamResolver implements PermissionsTeamResolver
{
    private static int|string|null $overrideTeamId = null;

    public function getPermissionsTeamId(): int|string|null
    {
        if (self::$overrideTeamId !== null) {
            return self::$overrideTeamId;
        }

        $tenant = Filament::getTenant();

        // Sem tenant selecionado, usar 0 como team global
        return $tenant?->getKey() ?? 0;
    }

    public function setPermissionsTeamId($id): void
    {
        self::$overrideTeamId = $id;
    }
}
