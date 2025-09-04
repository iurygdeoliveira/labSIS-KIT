<?php

declare(strict_types=1);

namespace App\Services;

class VideoMetadataService
{
    private const OEMBED_ENDPOINT = 'https://www.youtube.com/oembed';

    private const DEFAULT_USER_AGENT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

    private const DEFAULT_ACCEPT_LANGUAGE = 'en-US,en;q=0.8';

    public function getYoutubeTitle(string $videoUrl): string
    {
        $title = $this->fetchTitleFromOEmbed($videoUrl);
        if ($title !== '') {
            return $title;
        }

        $html = $this->curlGet($videoUrl);
        if ($html === '') {
            return '';
        }

        $ogTitle = $this->extractOgMeta($html, 'og:title');
        if ($ogTitle !== null) {
            return $ogTitle;
        }

        $fallback = $this->extractHtmlTitle($html);

        return $fallback ?? '';
    }

    public function getYoutubeMetadata(string $videoUrl): array
    {
        $title = $this->getYoutubeTitle($videoUrl);

        $html = $this->curlGet($videoUrl);
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
    }

    private function fetchTitleFromOEmbed(string $videoUrl): string
    {
        $endpoint = self::OEMBED_ENDPOINT.'?format=json&url='.rawurlencode($videoUrl);
        $json = $this->curlGet($endpoint);
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

    private function curlGet(string $url): string
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($ch === false) {
                return '';
            }

            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: '.self::DEFAULT_ACCEPT_LANGUAGE,
            ];

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_USERAGENT => self::DEFAULT_USER_AGENT,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);

            $response = curl_exec($ch);
            if ($response === false) {
                curl_close($ch);

                return '';
            }

            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status >= 200 && $status < 300) {
                return (string) $response;
            }

            return '';
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: '.self::DEFAULT_ACCEPT_LANGUAGE,
                    'User-Agent: '.self::DEFAULT_USER_AGENT,
                ]),
                'ignore_errors' => true,
                'timeout' => 15,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        return $response === false ? '' : (string) $response;
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
            $title = trim((string) $m[1]);
            $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5);
            // Remove sufixos comuns do YouTube
            $title = preg_replace('/\s*-\s*YouTube$/i', '', $title);

            return trim((string) $title);
        }

        return null;
    }

    private function extractLengthSecondsFromPlayerJson(string $html): ?int
    {
        // Procura por "lengthSeconds":"123" no JSON do player
        if (preg_match('/\"lengthSeconds\"\s*:\s*\"?(\d+)\"?/i', $html, $m) === 1) {
            return (int) $m[1];
        }

        return null;
    }

    private function extractIso8601FromHtml(string $html): ?string
    {
        if (preg_match('/itemprop=\"duration\"[^>]*content=\"([^\"]+)\"/i', $html, $m) === 1) {
            return (string) $m[1];
        }

        return null;
    }

    private function iso8601ToSeconds(string $iso8601): int
    {
        try {
            $interval = new \DateInterval($iso8601);
        } catch (\Throwable $e) {
            return 0;
        }

        $days = (int) $interval->d;
        $hours = (int) $interval->h;
        $minutes = (int) $interval->i;
        $seconds = (int) $interval->s;

        return ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;
    }
}
