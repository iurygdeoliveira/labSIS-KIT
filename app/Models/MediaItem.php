<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'media_items';

    protected $fillable = [
        'video_url',
    ];

    protected $appends = [
        'file_type',
        'human_size',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('media')
            ->useDisk('s3')
            ->singleFile();
    }

    public function getHumanSizeAttribute(): string
    {
        $size = (int) ($this->getFirstMedia('media')?->size ?? 0);

        if ($size < 1024) {
            return $size.' B';
        }

        if ($size < 1024 * 1024) {
            return round($size / 1024, 2).' KB';
        }

        if ($size < 1024 * 1024 * 1024) {
            return round($size / (1024 * 1024), 2).' MB';
        }

        return round($size / (1024 * 1024 * 1024), 2).' GB';
    }

    public function getFileTypeAttribute(): string
    {
        if (! empty($this->video_url)) {
            return 'Vídeo';
        }

        $mime = (string) ($this->getFirstMedia('media')?->mime_type ?? '');

        if ($mime === '') {
            return 'Desconhecido';
        }

        $main = explode('/', $mime)[0] ?? '';

        return match ($main) {
            'image' => 'Imagem',
            'video' => 'Vídeo',
            'audio' => 'Áudio',
            'application' => 'Documento',
            'text' => 'Texto',
            'font' => 'Fonte',
            default => ucfirst($main ?: 'Desconhecido'),
        };
    }

    public function getNameAttribute(): string
    {
        $attachment = $this->getFirstMedia('media');

        if ($attachment) {
            return (string) $attachment->name;
        }

        if (! empty($this->video_url)) {
            return 'Vídeo (URL)';
        }

        return 'Sem nome';
    }

    public function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('media');

        if (! $media) {
            return null;
        }

        try {
            return method_exists($media, 'getTemporaryUrl')
                ? $media->getTemporaryUrl(now()->addMinutes(10))
                : $media->getUrl();
        } catch (\Throwable) {
            return $media->getUrl();
        }
    }
}
