<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class VideoMetadataService
{
    public function __construct(
        private readonly HttpClient $httpClient
    ) {}

    public function getYoutubeTitle(string $videoUrl): string
    {
        $cacheKey = 'video:title:'.sha1($videoUrl);

        return Cache::store('redis')->remember($cacheKey, 6 * 3600, function () use ($videoUrl): string {
            $title = $this->fetchTitleFromOEmbed($videoUrl);
            if ($title !== '') {
                return $title;
            }

            $html = $this->httpClient->get($videoUrl);
            if ($html === '') {
                return '';
            }

            $ogTitle = $this->extractOgMeta($html, 'og:title');
            if ($ogTitle !== null) {
                return $ogTitle;
            }

            $fallback = $this->extractHtmlTitle($html);

            return $fallback ?? '';
        });
    }

    public function getYoutubeMetadata(string $videoUrl): array
    {
        $cacheKey = 'video:meta:'.sha1($videoUrl);

        return Cache::store('redis')->remember($cacheKey, 6 * 3600, function () use ($videoUrl): array {
            $title = $this->getYoutubeTitle($videoUrl);

            $html = $this->httpClient->get($videoUrl);
            $durationSeconds = null;
            $durationIso8601 = null;

            if ($html !== '') {
                $durationSeconds = $this->extractLengthSecondsFromPlayerJson($html);
                if ($durationSeconds === null) {
                    $durationIso8601 = $this->extractIso8601FromHtml($html);
                    if ($durationIso8601 !== null) {
                        $durationSeconds = $this->iso8601ToSeconds($durationIso8601);
                    }
                }
            }

            return [
                'title' => $title,
                'durationSeconds' => $durationSeconds,
                'durationIso8601' => $durationIso8601,
            ];
        });
    }

    private function fetchTitleFromOEmbed(string $videoUrl): string
    {
        $endpoint = config('video-metadata.youtube.oembed_endpoint').'?format=json&url='.rawurlencode($videoUrl);
        $json = $this->httpClient->get($endpoint);
        if ($json === '') {
            return '';
        }

        $data = json_decode($json, true);
        if (! is_array($data)) {
            return '';
        }

        $title = (string) ($data['title'] ?? '');

        return trim($title);
    }

    private function extractOgMeta(string $html, string $property): ?string
    {
        $pattern = '/<meta[^>]*property=\"'.preg_quote($property, '/').'\"[^>]*content=\"([^\"]+)\"[^>]*>/i';
        if (preg_match($pattern, $html, $m) === 1) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
        }

        return null;
    }

    private function extractHtmlTitle(string $html): ?string
    {
        if (preg_match('/<title>(.*?)<\/title>/is', $html, $m) === 1) {
            $title = trim($m[1]);
            $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5);
            $title = preg_replace('/\s*-\s*YouTube$/i', '', $title);

            return trim((string) $title);
        }

        return null;
    }

    private function extractLengthSecondsFromPlayerJson(string $html): ?int
    {
        if (preg_match('/\"lengthSeconds\"\s*:\s*\"?(\d+)\"?/i', $html, $m) === 1) {
            return (int) $m[1];
        }

        return null;
    }

    private function extractIso8601FromHtml(string $html): ?string
    {
        if (preg_match('/itemprop=\"duration\"[^>]*content=\"([^\"]+)\"/i', $html, $m) === 1) {
            return $m[1];
        }

        return null;
    }

    private function iso8601ToSeconds(string $iso8601): int
    {
        try {
            $interval = new \DateInterval($iso8601);
        } catch (\Throwable) {
            return 0;
        }

        $days = (int) $interval->d;
        $hours = (int) $interval->h;
        $minutes = (int) $interval->i;
        $seconds = (int) $interval->s;

        return ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;
    }
}
