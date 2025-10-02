<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\Computed;

class ViewTenant extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = TenantResource::class;

    #[Computed]
    public function tenantStats(): array
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

        $users = $record->users;
        $ownerCount = $users->filter(function ($user) use ($record) {
            return $user->isOwnerOfTenant($record);
        })->count();

        return [
            'user_count' => $users->count(),
            'owner_count' => $ownerCount,
            'regular_user_count' => $users->count() - $ownerCount,
            'is_active' => $record->is_active,
            'can_delete' => $users->count() === 0,
        ];
    }

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->users()->count() === 0;
    }

    #[Computed]
    public function canEdit(): bool
    {
        return $this->record?->is_active === true;
    }

    #[Computed]
    public function tenantPermissions(): array
    {
        return [
            'can_delete' => $this->canDelete,
            'can_edit' => $this->canEdit,
            'is_active' => $this->record?->is_active ?? false,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            // EditAction::make(),
        ];
    }
}
