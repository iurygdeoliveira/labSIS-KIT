<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Actions;

use App\Filament\Resources\Organization\OrganizationResource;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;

class DeleteOrganizationAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon(Heroicon::Trash)
            ->color('danger')
            ->visible(
                fn ($record): bool => Filament::auth()->user()?->can('delete', $record) ?? false
            )
            ->url(fn ($record): string => OrganizationResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
