<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Traits\Filament\HasBackButtonAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

/**
 * @property-read \App\Models\User|null $record
 * @property-read array $userStats
 * @property-read bool $canSuspend
 * @property-read bool $canUnsuspend
 * @property-read bool $canDelete
 * @property-read array $userPermissions
 */
class ViewUser extends ViewRecord
{
    use HasBackButtonAction;

    protected static string $resource = UserResource::class;

    #[Computed]
    public function userStats(): array
    {
        $record = $this->getRecord();

        if (! $record instanceof \App\Models\User) {
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
        $record = $this->getRecord();

        return $record instanceof \App\Models\User &&
               $record->getKey() !== Auth::id() &&
               ! $record->is_suspended;
    }

    #[Computed]
    public function canUnsuspend(): bool
    {
        $record = $this->getRecord();

        return $record instanceof \App\Models\User &&
               $record->getKey() !== Auth::id() &&
               $record->is_suspended;
    }

    #[Computed]
    public function canDelete(): bool
    {
        $record = $this->getRecord();

        return $record instanceof \App\Models\User && $record->getKey() !== Auth::id();
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
