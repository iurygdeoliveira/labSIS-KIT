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

        // Apenas páginas de Resource têm o método getResource()
        // @phpstan-ignore-next-line function.alreadyNarrowedType,staticMethod.notFound
        if (method_exists($this, 'getResource')) {
            /** @var class-string<\Filament\Resources\Resource> $resource */
            $resource = static::getResource();
            $action->url($resource::getUrl('index'));
        }

        return $action;
    }
}
