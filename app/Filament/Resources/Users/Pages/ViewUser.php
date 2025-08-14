<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Trait\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make(),
        ];
    }
}
