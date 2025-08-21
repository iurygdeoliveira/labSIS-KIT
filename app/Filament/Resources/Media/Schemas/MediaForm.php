<?php

namespace App\Filament\Resources\Media\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MediaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('model_type')
                    ->required(),
                TextInput::make('model_id')
                    ->required()
                    ->numeric(),
                TextInput::make('uuid')
                    ->label('UUID'),
                TextInput::make('collection_name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('file_name')
                    ->required(),
                TextInput::make('mime_type'),
                TextInput::make('disk')
                    ->required(),
                TextInput::make('conversions_disk'),
                TextInput::make('size')
                    ->required()
                    ->numeric(),
                Textarea::make('manipulations')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('custom_properties')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('generated_conversions')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('responsive_images')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('order_column')
                    ->numeric(),
            ]);
    }
}
