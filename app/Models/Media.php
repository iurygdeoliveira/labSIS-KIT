<?php

declare(strict_types=1);

namespace App\Models;

use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Media extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, UuidTrait;

    protected $fillable = [
        'uuid',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'conversions_disk',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'model_type',
        'model_id',
    ];

    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'generated_conversions' => 'array',
        'responsive_images' => 'array',
        'size' => 'integer',
        'order_column' => 'integer',
    ];

    protected $appends = [
        'human_size',
        'file_type',
        'is_image',
        'is_video',
        'is_document',
        'is_audio',
        'is_pdf',
    ];

    /**
     * Relacionamento polimórfico com outros modelos
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Tamanho do arquivo em formato humanizado
     */
    public function getHumanSizeAttribute(): string
    {
        if ($this->size < 1024) {
            return $this->size.' B';
        }

        if ($this->size < 1024 * 1024) {
            return round($this->size / 1024, 2).' KB';
        }

        if ($this->size < 1024 * 1024 * 1024) {
            return round($this->size / (1024 * 1024), 2).' MB';
        }

        return round($this->size / (1024 * 1024 * 1024), 2).' GB';
    }

    /**
     * Tipo de arquivo baseado no MIME type
     */
    public function getFileTypeAttribute(): string
    {
        if (! $this->mime_type) {
            return 'Desconhecido';
        }

        $types = [
            'image' => 'Imagem',
            'video' => 'Vídeo',
            'audio' => 'Áudio',
            'application' => 'Documento',
            'text' => 'Texto',
            'font' => 'Fonte',
        ];

        $mainType = explode('/', $this->mime_type)[0] ?? 'unknown';

        return $types[$mainType] ?? Str::title($mainType);
    }

    /**
     * Verifica se é uma imagem
     */
    public function getIsImageAttribute(): bool
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    /**
     * Verifica se é um vídeo
     */
    public function getIsVideoAttribute(): bool
    {
        return Str::startsWith($this->mime_type, 'video/');
    }

    /**
     * Verifica se é um documento
     */
    public function getIsDocumentAttribute(): bool
    {
        return Str::startsWith($this->mime_type, 'application/');
    }

    /**
     * Verifica se é um áudio
     */
    public function getIsAudioAttribute(): bool
    {
        return Str::startsWith($this->mime_type, 'audio/');
    }

    /**
     * Verifica se é um PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Registra as coleções de mídia
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('media')
            ->acceptsMimeTypes([
                'image/*',
                'audio/*',
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/plain',
            ]);
    }
}
