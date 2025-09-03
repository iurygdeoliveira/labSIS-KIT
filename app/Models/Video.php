<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'media_item_id',
        'provider',
        'provider_video_id',
        'url',
        'title',
        'description',
        'thumbnail_url',
        'duration_seconds',
        'width',
        'height',
        'aspect_ratio',
        'frame_rate',
        'bitrate_kbps',
        'has_audio',
        'audio_codec',
        'audio_channels',
        'audio_sample_rate_hz',
        'channel_id',
        'channel_name',
        'published_at',
        'view_count',
        'like_count',
        'comment_count',
        'is_live',
        'privacy_status',
        'license',
        'tags',
        'categories',
        'region_allowed',
        'region_blocked',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_live' => 'boolean',
            'has_audio' => 'boolean',
            'tags' => 'array',
            'categories' => 'array',
            'region_allowed' => 'array',
            'region_blocked' => 'array',
            'raw_payload' => 'array',
        ];
    }

    public function mediaItem()
    {
        return $this->belongsTo(MediaItem::class);
    }

    public function getResolutionAttribute(): ?string
    {
        if (! $this->width || ! $this->height) {
            return null;
        }

        return $this->width.'x'.$this->height;
    }

    public function getAspectRatioStringAttribute(): ?string
    {
        if (! $this->aspect_ratio) {
            return null;
        }

        // Converte aproximando para formatos populares
        $ratio = (float) $this->aspect_ratio;
        $popular = [
            '16:9' => 16 / 9,
            '4:3' => 4 / 3,
            '21:9' => 21 / 9,
            '1:1' => 1,
        ];

        $closest = null;
        $closestDiff = PHP_FLOAT_MAX;

        foreach ($popular as $label => $value) {
            $diff = abs($ratio - $value);
            if ($diff < $closestDiff) {
                $closest = $label;
                $closestDiff = $diff;
            }
        }

        return $closest ?? number_format($ratio, 2);
    }
}
