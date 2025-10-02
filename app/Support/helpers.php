<?php

declare(strict_types=1);

if (! function_exists('formatBytes')) {
    function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, $precision).' KB';
        }

        if ($bytes < 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024), $precision).' MB';
        }

        if ($bytes < 1024 * 1024 * 1024 * 1024) {
            return round($bytes / (1024 * 1024 * 1024), $precision).' GB';
        }

        return round($bytes / (1024 * 1024 * 1024 * 1024), $precision).' TB';
    }
}
