<?php

namespace App\Filament\Resources\Teams\Actions;

use App\Filament\Resources\Teams\TeamResource;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class DeleteTeamAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->url(fn ($record): string => TeamResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
