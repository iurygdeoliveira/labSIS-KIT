<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\NotificationsTrait;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateMedia extends CreateRecord
{
    use NotificationsTrait;

    protected static string $resource = MediaResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['media'], $data['video_preview']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->notifySuccess('Mídia criada com sucesso.');
    }
}
