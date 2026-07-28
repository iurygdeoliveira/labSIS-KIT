<?php

declare(strict_types=1);

namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string|null $project
 * @property string $version
 * @property Carbon|null $released_at
 * @property string $description
 * @property int $sort
 * @property string $uuid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Changelog extends Model
{
    use HasFactory;

    protected $table = 'changelogs';

    protected $fillable = [
        'uuid',
        'project',
        'version',
        'released_at',
        'description',
        'sort',
    ];

    protected static function booted(): void
    {
        static::creating(function (Changelog $entry): void {
            if (empty($entry->uuid)) {
                $entry->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'released_at' => 'date',
            'sort' => 'integer',
        ];
    }

    public function scopeProject(Builder $query, ?string $project): Builder
    {
        if ($project === null) {
            return $query;
        }

        return $query->where('project', $project);
    }
}
