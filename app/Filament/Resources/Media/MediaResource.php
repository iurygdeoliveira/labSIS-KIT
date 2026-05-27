<?php

namespace App\Filament\Resources\Media;

use App\Filament\Resources\Media\Pages\CreateMedia;
use App\Filament\Resources\Media\Pages\DeleteMedia;
use App\Filament\Resources\Media\Pages\EditMedia;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\Media\Pages\ViewMedia;
use App\Filament\Resources\Media\Schemas\MediaForm;
use App\Filament\Resources\Media\Schemas\MediaInfolist;
use App\Filament\Resources\Media\Tables\MediaTable;
use App\Models\MediaItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaResource extends Resource
{
    protected static ?string $model = MediaItem::class;

    /** @see MediaItem::team() */
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Photo;

    protected static ?string $navigationLabel = 'Mídias';

    protected static ?string $title = 'Mídias';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 2;

    #[Override]
    public static function getModelLabel(): string
    {
        return __('Media');
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'video',
            'team',
            'media' => fn ($mediaQuery) => $mediaQuery->where('collection_name', 'media'),
        ]);
    }

    #[Override]
    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    #[Override]
    public static function infolist(Schema $schema): Schema
    {
        return MediaInfolist::configure($schema);
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return MediaTable::configure($table)
            ->modifyQueryUsing(fn ($query) => $query->with([
                'video',
                'team',
                'media' => fn ($mediaQuery) => $mediaQuery->where('collection_name', 'media'),
            ]));
    }

    #[Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    #[Override]
    public static function getPages(): array
    {
        return [
            'index' => ListMedia::route('/'),
            'create' => CreateMedia::route('/create'),
            'view' => ViewMedia::route('/{record}'),
            'edit' => EditMedia::route('/{record}/edit'),
            'delete' => DeleteMedia::route('/{record}/delete'),
        ];
    }

    #[Override]
    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        /** @var MediaItem|null $record */
        if (! $record instanceof MediaItem) {
            return null;
        }

        if ((bool) $record->video) {
            $title = $record->linkedVideo()?->title;

            return $title ?: 'Vídeo (URL)';
        }

        $media = $record->getFirstMedia('media');

        return $media instanceof Media ? $media->name : 'Sem nome';
    }
}
