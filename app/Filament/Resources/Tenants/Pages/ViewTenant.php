<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            // EditAction::make(),
        ];
    }
}
