<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMedia extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make(),
        ];
    }
}
