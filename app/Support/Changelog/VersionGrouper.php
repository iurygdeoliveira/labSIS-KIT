<?php

declare(strict_types=1);

namespace App\Support\Changelog;


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

}
