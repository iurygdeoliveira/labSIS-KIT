<?php

namespace App\Listeners;

use App\Models\OrganizationInvite;
use Filament\Facades\Filament;
use Illuminate\Auth\Events\Login;

class AcceptPendingInvite
{
    public function handle(Login $event): void
    {
        $token = session()->pull('pending_invite_token');

        if (! $token) {
            return;
        }

        if (! OrganizationInvite::byToken($token)->pending()->exists()) {
            return;
        }

        $panel = Filament::getPanel('user');
        $path = $panel->getPath();
        $prefix = $path !== '' ? "/{$path}" : '';

        session()->put('url.intended', "{$prefix}/accept-invite/{$token}");
    }
}
