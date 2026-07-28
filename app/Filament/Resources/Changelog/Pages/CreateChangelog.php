<?php

declare(strict_types=1);

namespace App\Filament\Resources\Changelog\Pages;

use App\Filament\Resources\Changelog\ChangelogResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateChangelog extends CreateRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = ChangelogResource::class;

    #[Override]
    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Registro do changelog criado com sucesso.');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
