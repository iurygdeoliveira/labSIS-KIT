<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_item_id',
        'provider',
        'provider_video_id',
        'url',
        'title',
        'duration_seconds',
    ];

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
        ];
    }

    public function mediaItem(): BelongsTo
    {
        return $this->belongsTo(MediaItem::class);
    }
}
