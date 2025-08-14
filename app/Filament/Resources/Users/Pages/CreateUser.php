<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Trait\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Usuário criado com sucesso.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['email_verified_at'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
