<?php

namespace App\Traits\Filament;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

trait HasBackButtonAction
{
    protected function getBackButtonAction(): Action
    {
        $action = Action::make('back')
            ->label('Voltar')
            ->color('secondary')
            ->icon(Heroicon::ArrowLeft);

        if (method_exists(static::class, 'getResource')) {
            $action->url(static::getResource()::getUrl('index'));
        }

        return $action;
    }
}
