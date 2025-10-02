<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class ViewUser extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = UserResource::class;

    #[Computed]
    public function userStats(): array
    {
        $record = $this->record;

        if (! $record) {
            return [
                'tenant_count' => 0,
                'role_count' => 0,
                'is_owner_anywhere' => false,
                'last_login' => null,
                'is_current_user' => false,
            ];
        }

        return [
            'tenant_count' => $record->tenants()->count(),
            'role_count' => $record->rolesWithTeams()->count(),
            'is_owner_anywhere' => $record->hasOwnerRoleInAnyTenant(),
            'last_login' => $record->last_login_at?->diffForHumans(),
            'is_current_user' => $record->getKey() === Auth::id(),
        ];
    }

    #[Computed]
    public function canSuspend(): bool
    {
        return $this->record?->getKey() !== Auth::id() &&
               ! $this->record?->is_suspended;
    }

    #[Computed]
    public function canUnsuspend(): bool
    {
        return $this->record?->getKey() !== Auth::id() &&
               $this->record?->is_suspended;
    }

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->getKey() !== Auth::id();
    }

    #[Computed]
    public function userPermissions(): array
    {
        return [
            'can_suspend' => $this->canSuspend,
            'can_unsuspend' => $this->canUnsuspend,
            'can_delete' => $this->canDelete,
            'is_current_user' => $this->userStats['is_current_user'],
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
