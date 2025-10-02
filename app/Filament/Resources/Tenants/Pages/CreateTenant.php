<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Trait\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTenant extends CreateRecord
{
    use NotificationsTrait;

    protected static string $resource = TenantResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $data['is_active'] = true;

        $tenant = static::getModel()::create($data);
        if (! empty($users)) {
            $tenant->users()->sync($users);
        }

        return $tenant;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Tenant criado com sucesso.');
    }
}
