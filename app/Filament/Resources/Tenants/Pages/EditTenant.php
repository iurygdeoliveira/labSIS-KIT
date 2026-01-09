<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Computed;

/**
 * @property \App\Models\Tenant|null $record
 * @property-read bool $canDelete
 * @property-read bool $canEditUsers
 * @property-read int $userCount
 * @property-read array $tenantStats
 */
class EditTenant extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = TenantResource::class;

    public function canDelete(): bool
    {
        $record = $this->getRecord();

        return $record instanceof \App\Models\Tenant && $record->users()->count() === 0;
    }

    #[Computed]
    public function canEditUsers(): bool
    {
        return $this->record->is_active === true;
    }

    public function userCount(): int
    {
        $record = $this->getRecord();

        return $record instanceof \App\Models\Tenant ? $record->users()->count() : 0;
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

    #[\Override]
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (! $record instanceof \App\Models\Tenant) {
            return $record;
        }

        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $record->update($data);
        $record->users()->sync($users);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form'),
            $this->getCancelFormAction(),
        ];
    }

    #[\Override]
    protected function getFormActions(): array
    {
        return [];
    }

    #[\Override]
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
