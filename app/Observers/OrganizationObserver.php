<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Organization;
use App\Support\FilamentStatsCache;

class OrganizationObserver
{
    public function created(Organization $organization): void
    {
        $this->forgetOrganizationStats();
    }

    public function updated(Organization $organization): void
    {
        if ($organization->wasChanged('is_active')) {
            $this->forgetOrganizationStats();
        }
    }

    public function deleted(Organization $organization): void
    {
        $this->forgetOrganizationStats();
    }

    private function forgetOrganizationStats(): void
    {
        FilamentStatsCache::forgetTeams();
    }
}
