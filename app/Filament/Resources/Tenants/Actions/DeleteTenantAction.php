<?php

namespace App\Filament\Resources\Tenants\Actions;

use App\Filament\Resources\Tenants\TenantResource;
use Filament\Actions\Action;

class DeleteTenantAction
{
    public static function make(): Action
    {
        return Action::make('delete')
            ->label('Excluir')
            ->icon('heroicon-s-trash')
            ->color('danger')
            ->url(fn ($record) => TenantResource::getUrl('delete', ['record' => $record]))
            ->openUrlInNewTab(false);
    }
}
