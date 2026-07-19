<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\UuidTrait;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
/**
 * @property int $id
 * @property int $media_item_id
 * @property string|null $provider
 * @property string|null $provider_video_id
 * @property string $url
 * @property string|null $title
 * @property int|null $duration_seconds
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read MediaItem $mediaItem
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereMediaItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereProviderVideoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUrl($value)
 *
 * @mixin \Eloquent
 */
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'uuid',
    'organization_id',
    'media_item_id',
    'provider',
    'provider_video_id',
    'url',
    'title',
    'duration_seconds',
])]
class Video extends Model
{
    use HasFactory, UuidTrait;

    #[\Override]
    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<MediaItem, $this>
     */
    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class);
    }
}
