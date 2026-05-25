<?php

declare(strict_types=1);

namespace App\Filament\Resources\Security;

use App\Filament\Resources\Security\Pages\ListSecurityEvents;
use Override;
use WallaceMartinss\FilamentSecurity\Filament\Resources\SecurityEventResource as BaseSecurityEventResource;

class SecurityEventResource extends BaseSecurityEventResource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Administração';

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListSecurityEvents::route('/'),
        ];
    }
}
