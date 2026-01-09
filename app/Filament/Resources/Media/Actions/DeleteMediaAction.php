<?php

namespace App\Filament\Resources\Media\Actions;

use App\Filament\Resources\Media\MediaResource;
use Filament\Actions\Action;

class DeleteMediaAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon('heroicon-s-trash')
            ->color('danger')
            ->url(fn ($record): string => MediaResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
