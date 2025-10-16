<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Video;
use Illuminate\Support\Facades\Cache;

class VideoObserver
{
    public function created(Video $video): void
    {
        $this->forgetVideoMeta($video);
    }

    public function updated(Video $video): void
    {
        $this->forgetVideoMeta($video);
    }

    public function deleted(Video $video): void
    {
        $this->forgetVideoMeta($video);
    }

    private function forgetVideoMeta(Video $video): void
    {
        $url = (string) $video->url;
        if ($url !== '') {
            Cache::store('redis')->forget('video:meta:'.sha1($url));
            Cache::store('redis')->forget('video:title:'.sha1($url));
        }

        // Estatísticas de mídia podem ser afetadas
        Cache::store('redis')->forget('stats:media');
    }
}
