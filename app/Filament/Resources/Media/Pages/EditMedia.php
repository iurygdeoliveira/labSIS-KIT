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
}
