<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Models\Team;
use App\Models\User;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\Computed;

/**
 * @property Team|null $record
 * @property-read array $teamStats
 * @property-read bool $canDelete
 * @property-read bool $canEdit
 * @property-read array $teamPermissions
 */
class ViewTeam extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = TeamResource::class;

    #[Computed]
    public function teamStats(): array
    {
        $record = $this->record;

        if (! $record) {
            return [
                'user_count' => 0,
                'owner_count' => 0,
                'regular_user_count' => 0,
                'is_active' => false,
                'can_delete' => false,
            ];
        }

        $users = $record->members;
        $ownerCount = $users->filter(fn (User $user): bool => $user->isOwnerOfTeam($record))->count();

        return [
            'user_count' => $users->count(),
            'owner_count' => $ownerCount,
            'regular_user_count' => $users->count() - $ownerCount,
            'is_active' => $record->is_active,
            'can_delete' => $users->count() === 0,
        ];
    }

    public function canDelete(): bool
    {
        return $this->record->members()->count() === 0;
    }

    #[Computed]
    public function canEdit(): bool
    {
        return $this->record->is_active === true;
    }

    #[Computed]
    public function teamPermissions(): array
    {
        return [
            'can_delete' => $this->canDelete,
            'can_edit' => $this->canEdit,
            'is_active' => $this->record->is_active ?? false,
        ];
    }

    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
        ];
    }
}
