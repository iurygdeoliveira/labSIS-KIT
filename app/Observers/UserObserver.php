<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    public function updated(User $user): void
    {
        $this->forgetAvatarCache($user);
    }

    public function deleted(User $user): void
    {
        $this->forgetAvatarCache($user);
    }

    private function forgetAvatarCache(User $user): void
    {
        Cache::store('redis')->forget('user:'.$user->id.':avatar:temp-url');

        // Estatísticas de usuários podem ser afetadas
        Cache::store('redis')->forget('stats:users');
        // Tenants podem ser indiretamente afetados (aprovação/suspensão)
        Cache::store('redis')->forget('stats:tenants');
    }
}
