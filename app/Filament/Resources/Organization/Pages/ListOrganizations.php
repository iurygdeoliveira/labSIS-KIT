<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Pages;

use App\Filament\Resources\Organization\OrganizationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
