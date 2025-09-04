<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\HasBackButtonAction;
use App\Trait\Filament\NotificationsTrait;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = MediaResource::class;

    protected function getSavedNotificationTitle(): ?string
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            $this->getSaveFormAction()->formId('form'),
            DeleteAction::make()
                ->successNotificationTitle(null)
                ->after(function (): void {
                    $this->notifySuccess('Mídia excluída com sucesso.');
                }),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['media'], $data['video_preview'], $data['name']);

        $videoUrl = data_get($data, 'video.url');
        $data['video'] = ! empty($videoUrl);

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->getRecord();

        // Atualiza nome do arquivo (quando não for vídeo)
        $attachmentName = data_get($this->data, 'attachment_name');
        if (! (bool) $record->video && $attachmentName !== null && $attachmentName !== '') {
            if ($media = $record->getFirstMedia('media')) {
                $media->name = (string) $attachmentName;
                $media->save();
            }
        }

        // Atualiza título do vídeo (quando for vídeo)
        $videoTitle = data_get($this->data, 'video_title');
        if ((bool) $record->video && $videoTitle !== null && $videoTitle !== '') {
            $record->video()->update(['title' => (string) $videoTitle]);
        }

        $this->notifySuccess('Mídia atualizada com sucesso.');
        $this->redirect($this->getResource()::getUrl('index'));
    }
}
