<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\HasBackButtonAction;
use App\Trait\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateMedia extends CreateRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = MediaResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getCreateFormAction()
                ->label('Salvar')
                ->formId('form'),
            $this->getCreateAnotherFormAction()
                ->formId('form'),
            $this->getCancelFormAction()
                ->color('danger'),
        ];
    }

    #[Override]
    protected function getFormActions(): array
    {
        return [];
    }

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['media'], $data['video_preview']);

        // Se veio URL de vídeo no nested state, liga flag e desloca para model relacionado
        $videoUrl = $data['video']['url'] ?? null;
        if (! empty($videoUrl)) {
            $data['video'] = true;
        } else {
            $data['video'] = false;
        }

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function afterCreate(): void
    {
        $record = $this->getRecord();

        // Se foi informado vídeo na criação, persiste relação básica (URL)
        $videoUrl = data_get($this->data, 'video.url');
        if (! empty($videoUrl)) {
            $record->video()->create([
                'url' => $videoUrl,
            ]);
        }

        $this->notifySuccess('Mídia criada com sucesso.');
    }
}
