<?php

declare(strict_types=1);

namespace App\Support\Changelog;

use App\Enums\Changelog\ChangeType;

class KeepAChangelogParser
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $markdown): array
    {
        $entries = [];
        $sort = 0;

        $currentVersion = null;
        $currentReleasedAt = null;
        $currentIsReleased = false;
        $currentType = null;

        $lines = preg_split('/\r\n|\r|\n/', $markdown) ?: [];

        foreach ($lines as $line) {
            $trimmed = rtrim($line);

            // Version header:  ## [1.2.0] - 2024-01-15   |   ## [Unreleased]
            if (preg_match('/^##\s+(.+)$/', $trimmed, $m)) {
                [$currentVersion, $currentReleasedAt, $currentIsReleased] = $this->parseVersionHeading($m[1]);
                $currentType = null;

                continue;
            }

            // Type header:  ### Added
            if (preg_match('/^###\s+(.+)$/', $trimmed, $m)) {
                $currentType = ChangeType::fromHeading($m[1]) ?? ChangeType::Changed;

                continue;
            }

            // Bullet line:  - Something changed   |   * Something changed
            if ($currentVersion !== null && $currentType !== null
                && preg_match('/^\s*[-*]\s+(.+)$/', $trimmed, $m)) {
                $description = trim($m[1]);

                if ($description === '') {
                    continue;
                }

                $entries[] = [
                    'version' => $currentVersion,
                    'released_at' => $currentReleasedAt,
                    'is_released' => $currentIsReleased,
                    'type' => $currentType,
                    'description' => $description,
                    'sort' => $sort++,
                ];
            }
        }

        return $entries;
    }

    /**
     * @return array{0: string, 1: ?string, 2: bool}
     */
    protected function parseVersionHeading(string $heading): array
    {
        $heading = trim($heading);

        if (preg_match('/^\[([^\]]+)\]/', $heading, $m)) {
            $version = trim($m[1]);
        } elseif (preg_match('/^([^\s(]+)/', $heading, $m)) {
            $version = trim($m[1]);
        } else {
            $version = $heading;
        }

        $isReleased = strtolower($version) !== 'unreleased' && strtolower($version) !== 'não lançado';
        $releasedAt = $isReleased ? $this->extractDate($heading, $version) : null;

        return [$version, $releasedAt, $isReleased];
    }

    protected function extractDate(string $heading, string $version): ?string
    {
        $afterVersion = trim((string) preg_replace('/^\S+/', '', $heading));

        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $afterVersion, $m)) {
            return $m[1];
        }

        if (preg_match('/[-(]\s*([^)]+?)\s*\)?$/', $afterVersion, $m)) {
            $ts = strtotime(trim($m[1]));

            if ($ts !== false) {
                return date('Y-m-d', $ts);
            }
        }

        return null;
    }
}
