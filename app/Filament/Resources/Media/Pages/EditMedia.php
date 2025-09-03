<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Trait\Filament\HasBackButtonAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMedia extends EditRecord
{
    use HasBackButtonAction;

    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getBackButtonAction(),
            DeleteAction::make(),
        ];
    }

    // Após salvar, sincroniza mime_type e size no MediaItem
    protected function afterSave(): void
    {
        $record = $this->getRecord();

        $attachment = $record->getFirstMedia('media');

        if ($attachment) {
            $record->update([
                'mime_type' => $attachment->mime_type,
                'size' => $attachment->size,
            ]);
        } elseif (! empty($record->video_url)) {
            $record->update([
                'mime_type' => 'video/url',
                'size' => null,
            ]);
        } else {
            $record->update([
                'mime_type' => null,
                'size' => null,
            ]);
        }
    }
}
