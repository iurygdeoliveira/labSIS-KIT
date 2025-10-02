<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\Computed;

class EditMedia extends EditRecord
{
    use HasBackButtonAction;
    use NotificationsTrait;

    protected static string $resource = MediaResource::class;

    #[Computed]
    public function canDelete(): bool
    {
        return $this->record?->collection_name !== 'avatar';
    }

    #[Computed]
    public function fileSizeHuman(): string
    {
        $bytes = $this->record?->size ?? 0;

        return $this->humanSize($bytes);
    }

    #[Computed]
    public function mediaInfo(): array
    {
        $record = $this->record;

        if (! $record) {
            return [
                'can_delete' => false,
                'file_size_human' => '0 B',
                'mime_type' => '',
                'collection_name' => '',
            ];
        }

        return [
            'can_delete' => $this->canDelete,
            'file_size_human' => $this->fileSizeHuman,
            'mime_type' => $record->mime_type,
            'collection_name' => $record->collection_name,
        ];
    }

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

    private function humanSize(int $bytes): string
    {
        $gb = $bytes / (1024 * 1024 * 1024);
        $gbRounded = round($gb, 2);
        if ($gbRounded > 0) {
            return $gbRounded.' GB';
        }

        $mb = $bytes / (1024 * 1024);
        $mbRounded = round($mb, 2);
        if ($mbRounded > 0) {
            return $mbRounded.' MB';
        }

        $kb = $bytes / 1024;
        $kbRounded = round($kb, 2);
        if ($kbRounded > 0) {
            return $kbRounded.' KB';
        }

        return $bytes.' B';
    }
}
