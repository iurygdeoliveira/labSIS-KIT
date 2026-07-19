<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
/**
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property bool $video
 * @property string|null $mime_type
 * @property int|null $size
 * @property int|null $organization_id
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read string $file_type
 * @property-read string $human_size
 * @property-read string|null $image_url
 * @property-read string|null $collection_name
 * @property-read Video|null $videoRelation
 * @property-read MediaCollection<int, Media> $media
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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Appends([
    'file_type',
    'human_size',
])]
#[Fillable([
    'uuid',
    'name',
    'video',
    'mime_type',
    'size',
    'organization_id',
])]
#[Table(name: 'media_items')]
#[WithoutTimestamps]
class MediaItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, UuidTrait;

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

        if (! $media instanceof Media) {
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
     * @return HasOne<Video, $this>
     */
    public function video(): HasOne
    {
        return $this->hasOne(Video::class);
    }

    /**
     * Retorna o registro de vídeo quando {@see $video} (flag) é true.
     *
     * Necessário porque a coluna booleana `video` colide com a relação homônima.
     */
    public function linkedVideo(): ?Video
    {
        if (! $this->video) {
            return null;
        }

        if ($this->relationLoaded('video')) {
            $related = $this->getRelation('video');

            return $related instanceof Video ? $related : null;
        }

        return $this->video()->first();
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
