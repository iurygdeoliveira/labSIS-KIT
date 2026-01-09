<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property bool $video
 * @property string|null $mime_type
 * @property int|null $size
 * @property string|null $tenant_id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read string $file_type
 * @property-read string $human_size
 * @property-read string|null $image_url
 * @property-read string|null $collection_name
 * @property-read \App\Models\Video|null $videoRelation
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MediaItem whereVideo($value)
 *
 * @mixin \Eloquent
 */
use Spatie\MediaLibrary\InteractsWithMedia;

class MediaItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, UuidTrait;

    protected $table = 'media_items';

    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'name',
        'video',
        'mime_type',
        'size',
        'tenant_id',
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

    protected function getHumanSizeAttribute(): string
    {
        $size = (int) ($this->getFirstMedia('media')->size ?? 0);

        return formatBytes($size);
    }

    protected function getFileTypeAttribute(): string
    {
        if ($this->video) {
            return 'Vídeo';
        }

        $mime = (string) ($this->getFirstMedia('media')->mime_type ?? '');

        if ($mime === '') {
            return 'Desconhecido';
        }

        $main = explode('/', $mime)[0];

        return match ($main) {
            'image' => 'Imagem',
            'video' => 'Vídeo',
            'audio' => 'Áudio',
            'application' => 'Documento',
            'text' => 'Documento',
            'font' => 'Fonte',
            default => ucfirst($main ?: 'Desconhecido'),
        };
    }

    // Removido accessor de name: passamos a usar a coluna em banco

    protected function getImageUrlAttribute(): ?string
    {
        $media = $this->getFirstMedia('media');

        if (! $media instanceof \Spatie\MediaLibrary\MediaCollections\Models\Media) {
            return null;
        }

        try {
            return $media->getUrl();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function getMimeTypeAttribute(): ?string
    {
        return $this->getFirstMedia('media')?->mime_type;
    }

    protected function getSizeAttribute(): ?int
    {
        return $this->getFirstMedia('media')?->size;
    }

    protected function getCollectionNameAttribute(): ?string
    {
        return $this->getFirstMedia('media')?->collection_name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<\App\Models\Video, $this>
     */
    public function video(): HasOne
    {
        return $this->hasOne(Video::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
