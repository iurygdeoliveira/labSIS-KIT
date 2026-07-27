<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MediaItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function createFromUpload(UploadedFile $file, string $name, ?string $collection = 'media'): MediaItem
    {
        $item = MediaItem::create([
            'name' => $name,
            'video' => false,
        ]);

        $this->attachUploadedFile($item, $file, $collection ?? 'media');

        return $item;
    }

    public function createFromVideoUrl(string $videoUrl, string $name, ?string $collection = 'media'): MediaItem
    {
        return MediaItem::create([
            'name' => $name,
            'video' => $videoUrl,
        ]);
    }

    public function updateMedia(MediaItem $mediaItem, array $data): MediaItem
    {
        if (isset($data['video']) && ! empty($data['video'])) {
            if ($mediaItem->getFirstMedia() instanceof Media) {
                $mediaItem->clearMediaCollection();
            }

            $mediaItem->update([
                'name' => $data['name'] ?? $mediaItem->name,
                'video' => $data['video'],
            ]);

            return $mediaItem;
        }

        if (isset($data['media']) && $data['media'] instanceof UploadedFile) {
            $mediaItem->update([
                'name' => $data['name'] ?? $mediaItem->name,
                'video' => false,
            ]);

            $this->attachUploadedFile($mediaItem, $data['media'], 'media');

            return $mediaItem;
        }

        if (isset($data['name'])) {
            $mediaItem->update(['name' => $data['name']]);
        }

        return $mediaItem;
    }

    protected function attachUploadedFile(MediaItem $item, UploadedFile $file, string $collection = 'media'): void
    {
        $mime = (string) $file->getMimeType();

        if (str_starts_with($mime, 'image/') && ! in_array($mime, ['image/svg+xml', 'image/gif', 'image/avif'], true)) {
            try {
                $avifImage = Image::fromUpload($file)->toAvif()->quality(80);
                $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.avif';

                $item->addMediaFromString($avifImage->toBytes())
                    ->usingFileName($filename)
                    ->toMediaCollection($collection);

                return;
            } catch (\Throwable) {
                // Fallback para upload original caso ocorra erro no driver
            }
        }

        $item->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection($collection);
    }

    public function getMediaUrl(MediaItem $media): ?string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return $videoUrl;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia instanceof Media ? $spatieMedia->getUrl() : null;
    }

    public function getMediaPath(MediaItem $media): ?string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return null;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia instanceof Media ? $spatieMedia->getPath() : null;
    }

    public function getMediaType(MediaItem $media): string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return 'video_url';
        }

        $spatieMedia = $media->getFirstMedia();
        if (! $spatieMedia instanceof Media) {
            return 'unknown';
        }

        $mime = (string) $spatieMedia->mime_type;
        if (str_starts_with($mime, 'image/')) {
            return 'image';
        }
        if (str_starts_with($mime, 'audio/')) {
            return 'audio';
        }
        if (str_starts_with($mime, 'video/')) {
            return 'video';
        }
        if (str_starts_with($mime, 'application/')) {
            return 'document';
        }

        return 'unknown';
    }
}
