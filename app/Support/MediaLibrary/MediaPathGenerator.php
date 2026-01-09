<?php

declare(strict_types=1);

namespace App\Support\MediaLibrary;

use Filament\Facades\Filament;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        $folder = $media->collection_name === 'avatar'
            ? 'avatar'
            : $this->getFolderByMimeType($media->mime_type);

        $tenant = Filament::getTenant();
        $prefix = $tenant ? 'tenants/'.$tenant->getKey().'/' : '';

        return "{$prefix}{$folder}/{$media->id}/";
    }

    public function getPathForConversions(Media $media): string
    {
        $folder = $media->collection_name === 'avatar'
            ? 'avatar'
            : $this->getFolderByMimeType($media->mime_type);

        $tenant = Filament::getTenant();
        $prefix = $tenant ? 'tenants/'.$tenant->getKey().'/' : '';

        return "{$prefix}{$folder}/{$media->id}/conversions/";
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        $folder = $media->collection_name === 'avatar'
            ? 'avatar'
            : $this->getFolderByMimeType($media->mime_type);

        $tenant = Filament::getTenant();
        $prefix = $tenant ? 'tenants/'.$tenant->getKey().'/' : '';

        return "{$prefix}{$folder}/{$media->id}/responsive-images/";
    }

    private function getFolderByMimeType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'images';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audios';
        }

        return 'documents';
    }
}
