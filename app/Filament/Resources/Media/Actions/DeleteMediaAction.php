<?php

namespace App\Filament\Resources\Media\Actions;

use App\Filament\Resources\Media\MediaResource;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class DeleteMediaAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->url(fn ($record): string => MediaResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
