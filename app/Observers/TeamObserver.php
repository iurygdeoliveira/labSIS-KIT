<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Team;
use App\Support\FilamentStatsCache;

class TeamObserver
{
    public function created(Team $team): void
    {
        $this->forgetTeamStats();
    }

    public function updated(Team $team): void
    {
        if ($team->wasChanged('is_active')) {
            $this->forgetTeamStats();
        }
    }

    public function deleted(Team $team): void
    {
        $this->forgetTeamStats();
    }

    private function forgetTeamStats(): void
    {
        FilamentStatsCache::forgetTeams();
    }
}
