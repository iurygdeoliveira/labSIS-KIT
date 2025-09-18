<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Trait\Filament\HasBackButtonAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTenant extends EditRecord
{
    use HasBackButtonAction;

    protected static string $resource = TenantResource::class;

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
}
