<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Services\VideoMetadataService;
use App\Traits\Filament\HasBackButtonAction;
use App\Traits\Filament\NotificationsTrait;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
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

        // Se veio URL de vídeo no nested state, liga flag e garante nome amigável
        $videoUrl = $data['video']['url'] ?? null;
        if (! empty($videoUrl)) {
            $data['video'] = true;

            // Define 'name' baseado no título informado ou no ID do provedor (YouTube)
            $friendlyTitle = (string) (data_get($this->data, 'video_title') ?? '');
            $candidate = $friendlyTitle !== ''
                ? $friendlyTitle
                : (string) ($this->extractYoutubeId($videoUrl) ?? 'video');

            $data['name'] = Str::slug($candidate) ?: 'video';
        } else {
            $data['video'] = false;

            // No fluxo de upload, o 'name' é preenchido no estado do form.
            // Por segurança, define fallback se vier vazio.
            if (empty($data['name'] ?? null)) {
                $data['name'] = 'arquivo';
            }
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
        // Garante que relações e anexos salvos estejam disponíveis
        $record->refresh();

        // Se foi informado vídeo na criação, persiste relação básica (URL)
        $videoUrl = data_get($this->data, 'video.url');
        if (! empty($videoUrl)) {
            $video = $record->video()->create([
                'url' => $videoUrl,
            ]);

            try {
                if ($this->isYoutubeUrl($videoUrl)) {
                    $service = app(VideoMetadataService::class);
                    $meta = $service->getYoutubeMetadata($videoUrl);

                    $update = [];

                    if (($meta['title'] ?? '') !== '') {
                        $update['title'] = (string) $meta['title'];
                        $update['provider'] = 'youtube';
                    }

                    if (($meta['durationSeconds'] ?? null) !== null) {
                        $update['duration_seconds'] = (int) $meta['durationSeconds'];
                    }

                    $providerVideoId = $this->extractYoutubeId($videoUrl);
                    if ($providerVideoId !== null) {
                        $update['provider_video_id'] = $providerVideoId;
                        $update['provider'] = 'youtube';
                    }

                    // Aplica título amigável informado no formulário (se houver)
                    $friendlyTitle = (string) (data_get($this->data, 'video_title') ?? '');
                    if ($friendlyTitle !== '') {
                        $update['title'] = $friendlyTitle;
                    }

                    if (! empty($update)) {
                        $video->update($update);
                    }
                }
            } catch (\Throwable) {
                // silencioso
            }
        }

        // Renomeação do anexo deve ocorrer após relacionamentos serem salvos
        $this->afterSaveRenameAttachment($record);

        $this->notifySuccess('Mídia criada com sucesso.');
    }

    private function afterSaveRenameAttachment($record): void
    {
        $videoUrl = data_get($this->data, 'video.url');

        // Se for arquivo (não vídeo), aplica nome amigável no anexo do Spatie
        if (empty($videoUrl)) {
            $attachmentName = data_get($this->data, 'attachment_name');
            if ($attachmentName !== null && $attachmentName !== '') {
                if ($media = $record->getFirstMedia('media')) {
                    $media->name = (string) $attachmentName;
                    $media->save();
                }
            }
        }
    }

    private function isYoutubeUrl(?string $url): bool
    {
        if ($url === null || $url === '') {
            return false;
        }

        $host = (string) parse_url($url, PHP_URL_HOST);
        $host = str_replace('www.', '', strtolower($host));

        return in_array($host, ['youtube.com', 'm.youtube.com', 'youtu.be'], true);
    }

    private function extractYoutubeId(string $url): ?string
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $host = str_replace('www.', '', strtolower($host));
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');

        if ($host === 'youtu.be') {
            $parts = array_values(array_filter(explode('/', $path)));

            return $parts[0] ?? null;
        }

        if ($host === 'youtube.com' || $host === 'm.youtube.com') {
            parse_str((string) (parse_url($url, PHP_URL_QUERY) ?? ''), $query);
            if (! empty($query['v'])) {
                return (string) $query['v'];
            }

            $parts = array_values(array_filter(explode('/', $path)));
            if (($parts[0] ?? '') === 'shorts' && ! empty($parts[1] ?? '')) {
                return (string) $parts[1];
            }
        }

        return null;
    }
}
