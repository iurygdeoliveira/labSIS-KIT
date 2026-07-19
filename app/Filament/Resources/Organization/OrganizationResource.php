<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization;

use App\Models\Organization;
use App\Traits\Filament\HasConfigurableNavigationSort;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('organization.fields.name'))
                    ->required(),
                TextInput::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->required()
                    ->unique(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('organization.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('organization.fields.slug'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->label(__('organization.members.title'))
                    ->counts('users')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('organization.fields.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
