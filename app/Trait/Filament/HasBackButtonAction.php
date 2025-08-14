<?php

namespace App\Trait\Filament;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

trait HasBackButtonAction
{
    protected function getBackButtonAction(): Action
    {
        return Action::make('back')
            ->label('Voltar')
            ->color('secondary')
            ->icon(Heroicon::ArrowLeft)
            ->url(static::getResource()::getUrl('index'));
    }
}
