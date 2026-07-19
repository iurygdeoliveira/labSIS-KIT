<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization;

use App\Traits\Filament\HasConfigurableNavigationSort;
use Guidance\FilamentTenantMembers\Filament\AdminPanel\Resources\OrganizationResource as BaseOrganizationResource;

class OrganizationResource extends BaseOrganizationResource
{
    use HasConfigurableNavigationSort;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Organizações';

    protected static ?string $modelLabel = 'Organização';

    protected static ?string $slug = 'organizations';

    protected static ?string $pluralModelLabel = 'Organizações';

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
