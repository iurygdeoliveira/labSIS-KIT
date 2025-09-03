<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MediaItem;
use Illuminate\Http\UploadedFile;

class MediaService
{
    /**
     * Cria um registro de mídia a partir de um arquivo enviado
     */
    public function createFromUpload(UploadedFile $file, string $name, ?string $collection = 'media'): MediaItem
    {
        $item = MediaItem::create([
            'name' => $name,
            'video_url' => null,
        ]);

        // Adiciona o arquivo usando o Spatie Media Library
        $item->addMedia($file)
            ->usingFileName($file->getClientOriginalName())
            ->toMediaCollection($collection);

        return $item;
    }

    /**
     * Cria um registro de mídia a partir de uma URL de vídeo
     */
    public function createFromVideoUrl(string $videoUrl, string $name, ?string $collection = 'media'): MediaItem
    {
        return MediaItem::create([
            'name' => $name,
            'video_url' => $videoUrl,
        ]);
    }

    /**
     * Atualiza um registro de mídia existente
     */
    public function updateMedia(MediaItem $mediaItem, array $data): MediaItem
    {
        // Se está mudando de arquivo para URL de vídeo
        if (isset($data['video_url']) && ! empty($data['video_url'])) {
            // Remove o arquivo se existir
            if ($mediaItem->getFirstMedia()) {
                $mediaItem->clearMediaCollection();
            }

            $mediaItem->update([
                'name' => $data['name'] ?? $mediaItem->name,
                'video_url' => $data['video_url'],
            ]);

            return $mediaItem;
        }

        // Se está mudando de URL de vídeo para arquivo
        if (isset($data['media']) && $data['media'] instanceof UploadedFile) {
            $mediaItem->update([
                'name' => $data['name'] ?? $mediaItem->name,
                'video_url' => null,
            ]);

            // Adiciona o novo arquivo
            $spatieMedia = $mediaItem->addMedia($data['media'])
                ->usingFileName($data['media']->getClientOriginalName())
                ->toMediaCollection('media');

            return $mediaItem;
        }

        // Atualização simples (apenas nome)
        if (isset($data['name'])) {
            $mediaItem->update(['name' => $data['name']]);
        }

        return $mediaItem;
    }

    /**
     * Obtém a URL do arquivo ou vídeo
     */
    public function getMediaUrl(MediaItem $media): ?string
    {
        if (! empty($media->video_url)) {
            return $media->video_url;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia ? $spatieMedia->getUrl() : null;
    }

    /**
     * Obtém o caminho do arquivo no storage
     */
    public function getMediaPath(MediaItem $media): ?string
    {
        if (! empty($media->video_url)) {
            return null;
        }

        $spatieMedia = $media->getFirstMedia();

        return $spatieMedia ? $spatieMedia->getPath() : null;
    }

    /**
     * Verifica se a mídia é um arquivo (não URL)
     */
    public function isFile(MediaItem $media): bool
    {
        return empty($media->video_url);
    }

    /**
     * Verifica se a mídia é uma URL de vídeo
     */
    public function isVideoUrl(MediaItem $media): bool
    {
        return ! empty($media->video_url);
    }

    /**
     * Obtém o tipo de mídia para organização
     */
    public function getMediaType(MediaItem $media): string
    {
        if (! empty($media->video_url)) {
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
