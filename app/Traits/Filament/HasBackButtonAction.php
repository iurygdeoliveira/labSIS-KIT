<?php

namespace App\Traits\Filament;

use Filament\Actions\Action;

trait HasBackButtonAction
{
    protected function getBackButtonAction(): Action
    {
        $action = Action::make('back')
            ->label('Voltar')
            ->color('secondary')
            ->icon('heroicon-s-arrow-left');

        // @phpstan-ignore function.alreadyNarrowedType
        if (method_exists(static::class, 'getResource')) {
            /** @var class-string<\Filament\Resources\Resource> $resource */
            $resource = static::getResource();
            $action->url($resource::getUrl('index'));
        }

        return $action;
    }
}
