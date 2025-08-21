<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MediaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('model_type'),
                TextEntry::make('model_id')
                    ->numeric(),
                TextEntry::make('uuid')
                    ->label('UUID'),
                TextEntry::make('collection_name'),
                TextEntry::make('name'),
                TextEntry::make('file_name'),
                TextEntry::make('mime_type'),
                TextEntry::make('disk'),
                TextEntry::make('conversions_disk'),
                TextEntry::make('size')
                    ->numeric(),
                TextEntry::make('order_column')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
