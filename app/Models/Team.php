<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LaravelDaily\FilaTeams\Models\Team as BaseTeam;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property bool $is_personal
 * @property bool $is_active
 * @property CarbonImmutable|null $deleted_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, MediaItem> $mediaItems
 * @property-read int|null $media_items_count
 *
 * @method static \Database\Factories\TeamFactory factory($count = null, $state = [])
 */
class Team extends BaseTeam
{
    /** @var class-string<TeamFactory> */
    protected static string $factory = TeamFactory::class;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'slug',
        'is_personal',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<MediaItem, $this>
     */
    public function mediaItems(): HasMany
    {
        return $this->hasMany(MediaItem::class);
    }
}
