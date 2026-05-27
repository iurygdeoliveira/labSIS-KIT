<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use App\Support\FilamentStatsCache;

class UserObserver
{
    public function created(User $user): void
    {
        $this->forgetUserStats();
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged(['is_approved', 'is_suspended', 'email_verified_at'])) {
            $this->forgetUserStats();
        }
    }

    public function deleted(User $user): void
    {
        $this->forgetUserStats();
        FilamentStatsCache::forgetTeams();
    }

    private function forgetUserStats(): void
    {
        FilamentStatsCache::forgetUsers();
    }
}
