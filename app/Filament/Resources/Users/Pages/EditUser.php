<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Trait\Filament\Actions\HasBackButtonAction;
use App\Trait\NotificationsTrait;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(Notification::make())
                ->after(fn () => $this->notifySuccess('Usuário excluído com sucesso.')),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->notifySuccess('Usuário atualizado com sucesso.');
    }
}
