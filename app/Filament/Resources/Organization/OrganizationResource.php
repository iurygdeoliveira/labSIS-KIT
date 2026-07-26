<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization;

use App\Filament\Resources\Organization\Pages\CreateOrganization;
use App\Filament\Resources\Organization\Pages\DeleteOrganization;
use App\Filament\Resources\Organization\Pages\EditOrganization;
use App\Filament\Resources\Organization\Pages\ListOrganizations;
use App\Filament\Resources\Organization\Pages\ViewOrganization;
use App\Filament\Resources\Organization\Schemas\OrganizationForm;
use App\Filament\Resources\Organization\Schemas\OrganizationInfolist;
use App\Filament\Resources\Organization\Tables\OrganizationTable;
use App\Models\Organization;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

class OrganizationResource extends Resource
{
    use HasConfigurableNavigationSort;

    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Organizações';

    protected static ?string $modelLabel = 'Organização';

    protected static ?string $slug = 'organizations';

    protected static ?string $pluralModelLabel = 'Organizações';

    protected static ?string $recordTitleAttribute = 'name';

    #[Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'slug';
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return OrganizationInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return OrganizationTable::configure($table);
    }

    #[Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'view' => ViewOrganization::route('/{record}'),
            'edit' => EditOrganization::route('/{record}/edit'),
            'delete' => DeleteOrganization::route('/{record}/delete'),
        ];
    }
}
