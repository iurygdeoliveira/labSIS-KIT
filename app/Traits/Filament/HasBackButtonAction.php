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

        // @phpstan-ignore function.alreadyNarrowedType
        if (method_exists(static::class, 'getResource')) {
            /** @var class-string<\Filament\Resources\Resource> $resource */
            $resource = static::getResource();
            $action->url($resource::getUrl('index'));
        }

        return $action;
    }
}
