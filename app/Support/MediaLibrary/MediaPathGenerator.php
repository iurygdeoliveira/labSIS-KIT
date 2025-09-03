<?php

declare(strict_types=1);

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);

        return "{$folder}/{$media->id}/";
    }

    public function getPathForConversions(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);

        return "{$folder}/{$media->id}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $folder = $this->getFolderByMimeType($media->mime_type);

        return "{$folder}/{$media->id}/responsive-images/";
    }

    private function getFolderByMimeType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'images';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audios';
        }

        // Vídeos não são armazenados no MinIO, apenas URLs no banco
        if (str_starts_with($mimeType, 'document/')) {
            return 'documents';
        }

        return 'documents';
    }
}
