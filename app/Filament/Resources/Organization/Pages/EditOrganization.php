<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organization\Pages;

use App\Filament\Resources\Organization\OrganizationResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Override;

class EditOrganization extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = OrganizationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            Action::make('delete')
                ->label('Excluir')
                ->icon(Heroicon::Trash)
                ->color('danger')
                ->url(fn (): string => OrganizationResource::getUrl('delete', ['record' => $this->getRecord()])),
        ];
    }

    #[Override]
    protected function getSavedNotification(): ?Notification
    {
        return null;
    }

    protected function afterSave(): void
    {
        $this->notifySuccess('Organização atualizada com sucesso.');
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
