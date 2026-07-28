<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Pages;

use App\Filament\Resources\Changelog\ChangelogResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Override;

class ViewChangelog extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = ChangelogResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make(),
        ];
    }
}
