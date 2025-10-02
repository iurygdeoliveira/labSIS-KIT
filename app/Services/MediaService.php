<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MediaItem;
use Illuminate\Http\UploadedFile;

class MediaService
{
    public function createFromUpload(UploadedFile $file, string $name, ?string $collection = 'media'): MediaItem
    {
        $item = MediaItem::create([
            'name' => $name,
            'video' => null,
        ]);

        $item->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection($collection);

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
            if ($mediaItem->getFirstMedia()) {
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
                'video' => null,
            ]);

            $mediaItem->addMedia($data['media'])
                ->usingFileName($data['media']->getClientOriginalName())
                ->toMediaCollection('media');

            return $mediaItem;
        }

        if (isset($data['name'])) {
            $mediaItem->update(['name' => $data['name']]);
        }

        return $mediaItem;
    }

    public function getMediaUrl(MediaItem $media): ?string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return $videoUrl;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia ? $spatieMedia->getUrl() : null;
    }

    public function getMediaPath(MediaItem $media): ?string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return null;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia ? $spatieMedia->getPath() : null;
    }

    public function getMediaType(MediaItem $media): string
    {
        $videoUrl = $media->getAttributes()['video'] ?? null;

        if (! empty($videoUrl)) {
            return 'video_url';
        }

        $spatieMedia = $media->getFirstMedia();
        if (! $spatieMedia) {
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
