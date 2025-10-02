<?php

declare(strict_types=1);

namespace App\Services;

class HttpClient
{
    public function get(string $url): string
    {
        $userAgent = config('video-metadata.http.user_agent');
        $acceptLanguage = config('video-metadata.http.accept_language');
        $connectTimeout = config('video-metadata.http.connect_timeout');
        $timeout = config('video-metadata.http.timeout');
        $maxRedirects = config('video-metadata.http.max_redirects');

        if (function_exists('curl_init')) {
            return $this->curlGet($url, $userAgent, $acceptLanguage, $connectTimeout, $timeout, $maxRedirects);
        }

        return $this->fileGetContents($url, $userAgent, $acceptLanguage, $timeout);
    }

    private function curlGet(
        string $url,
        string $userAgent,
        string $acceptLanguage,
        int $connectTimeout,
        int $timeout,
        int $maxRedirects
    ): string {
        $ch = curl_init($url);
        if ($ch === false) {
            return '';
        }

        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            "Accept-Language: {$acceptLanguage}",
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => $maxRedirects,
            CURLOPT_CONNECTTIMEOUT => $connectTimeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_USERAGENT => $userAgent,
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

    private function fileGetContents(
        string $url,
        string $userAgent,
        string $acceptLanguage,
        int $timeout
    ): string {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    "Accept-Language: {$acceptLanguage}",
                    "User-Agent: {$userAgent}",
                ]),
                'ignore_errors' => true,
                'timeout' => $timeout,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);

        return $response === false ? '' : (string) $response;
    }
}
