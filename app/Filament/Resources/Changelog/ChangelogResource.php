<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog;

use App\Filament\Resources\Changelog\Pages\CreateChangelog;
use App\Filament\Resources\Changelog\Pages\DeleteChangelog;
use App\Filament\Resources\Changelog\Pages\EditChangelog;
use App\Filament\Resources\Changelog\Pages\ListChangelogs;
use App\Filament\Resources\Changelog\Pages\ViewChangelog;
use App\Filament\Resources\Changelog\Schemas\ChangelogForm;
use App\Filament\Resources\Changelog\Schemas\ChangelogInfolist;
use App\Filament\Resources\Changelog\Tables\ChangelogTable;
use App\Models\Changelog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Override;

class ChangelogResource extends Resource
{
    protected static ?string $model = Changelog::class;

    protected static ?string $recordTitleAttribute = 'description';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Atualizações';

    protected static ?int $navigationSort = 90;

    protected static ?string $modelLabel = 'Atualizações';

    protected static ?string $pluralModelLabel = 'Atualizações';

    #[Override]
    public static function getRecordRouteKeyName(): string
    {
        return 'uuid';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return ChangelogForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return ChangelogInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return ChangelogTable::configure($table);
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListChangelogs::route('/'),
            'create' => CreateChangelog::route('/create'),
            'view' => ViewChangelog::route('/{record}'),
            'edit' => EditChangelog::route('/{record}/edit'),
            'delete' => DeleteChangelog::route('/{record}/delete'),
        ];
    }
}
