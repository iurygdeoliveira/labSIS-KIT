<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Services\VideoMetadataService;
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
        unset($data['media'], $data['video_preview'], $data['name']);

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

                    if (! empty($update)) {
                        $video->update($update);
                    }
                }
            } catch (\Throwable) {
                // silencioso
            }
        }

        $this->notifySuccess('Mídia criada com sucesso.');
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
