<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTenant extends CreateRecord
{
    use NotificationsTrait;

    protected static string $resource = TenantResource::class;

    #[\Override]
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    #[\Override]
    protected function handleRecordCreation(array $data): Model
    {
        $users = (array) ($data['usersIds'] ?? []);
        unset($data['usersIds']);

        $data['is_active'] = true;

        $tenant = static::getModel()::create($data);

        if (! $tenant instanceof \App\Models\Tenant) {
            return $tenant;
        }

        if ($users !== []) {
            $tenant->users()->sync($users);
        }

        return $tenant;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Tenant criado com sucesso.');
    }
}
