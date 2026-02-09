<?php

namespace App\Filament\Resources\Users\Actions;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;

class DeleteUserAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon('heroicon-s-trash')
            ->color('danger')
            ->visible(
                fn ($record): bool => \Filament\Facades\Filament::auth()->user()?->can('delete', $record) ?? false
            )
            ->url(fn ($record): string => UserResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
