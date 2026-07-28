<?php

declare(strict_types=1);

namespace App\Support\Changelog;

use App\Enums\Changelog\ChangeType;
use App\Models\Changelog;
use Illuminate\Support\Collection;

class VersionGrouper
{
    /**
     * @param  Collection<int, Changelog>  $entries
     * @return Collection<string, Collection<int, Changelog>>
     */
    public function group(Collection $entries): Collection
    {
        return collect($entries->all())
            ->groupBy('version')
            ->sortBy(function (Collection $group, string $version): string {
                if (strtolower($version) === 'unreleased' || strtolower($version) === 'não lançado') {
                    return '9999-99-99';
                }

                $date = optional($group->first())->released_at;

                return $date ? $date->format('Y-m-d') : '0000-00-00';
            })
            ->reverse();
    }

    /**
     * @param  Collection<int, Changelog>  $group
     * @return Collection<int, array{type: ChangeType, entries: Collection<int, Changelog>}>
     */
    public function byType(Collection $group): Collection
    {
        return collect(ChangeType::cases())
            ->map(fn (ChangeType $type): array => [
                'type' => $type,
                'entries' => $group
                    ->filter(fn (Changelog $e): bool => $e->type === $type)
                    ->sortBy('sort')
                    ->values(),
            ])
            ->filter(fn (array $bucket): bool => $bucket['entries']->isNotEmpty())
            ->values();
    }
}
