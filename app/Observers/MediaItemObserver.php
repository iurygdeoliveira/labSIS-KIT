<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\MediaItem;
use App\Support\FilamentStatsCache;

class MediaItemObserver
{
    public function created(MediaItem $mediaItem): void
    {
        $this->forgetMediaStats();
    }

    public function updated(MediaItem $mediaItem): void
    {
        $this->forgetMediaStats();
    }

    public function deleted(MediaItem $mediaItem): void
    {
        $this->forgetMediaStats();
    }

    private function forgetMediaStats(): void
    {
        FilamentStatsCache::forgetMedia();
    }
}
