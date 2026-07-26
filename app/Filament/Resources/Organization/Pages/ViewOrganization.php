<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Pages;

use App\Filament\Resources\Organization\OrganizationResource;
use App\Models\Organization;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Computed;

/**
 * @property-read Organization|null $record
 * @property-read array $organizationStats
 */
class ViewOrganization extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = OrganizationResource::class;

    #[Computed]
    public function organizationStats(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof Organization) {
            return [
                'users_count' => 0,
            ];
        }

        return [
            'users_count' => $record->users()->count(),
        ];
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            EditAction::make()
                ->icon(Heroicon::PencilSquare),
        ];
    }
}
