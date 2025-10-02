<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

class EditTenant extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = TenantResource::class;

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->users()->count() === 0;
    }

    #[Computed]
    public function canEditUsers(): bool
    {
        return $this->record?->is_active === true;
    }

    #[Computed]
    public function userCount(): int
    {
        return $this->record?->users()->count() ?? 0;
    }

    #[Computed]
    public function tenantStats(): array
    {
        $record = $this->record;

        if (! $record) {
            return [
                'user_count' => 0,
                'is_active' => false,
                'can_delete' => false,
                'can_edit_users' => false,
            ];
        }

        return [
            'user_count' => $this->userCount,
            'is_active' => $record->is_active,
            'can_delete' => $this->canDelete,
            'can_edit_users' => $this->canEditUsers,
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $record->update($data);
        $record->users()->sync($users);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        $actions = [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form'),
            $this->getCancelFormAction(),
        ];

        return $actions;
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {

        $this->notifySuccess('Tenant atualizado com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
