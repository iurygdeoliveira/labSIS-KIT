<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Pages;

use App\Filament\Resources\Organization\OrganizationResource;
use Filament\Resources\Pages\EditRecord;

class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;
}
