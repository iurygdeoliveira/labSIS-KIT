<?php

namespace App\Listeners;

use App\Models\AuthenticationLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LogAuthenticationActivity
{
    public function __construct(public Request $request) {}

    public function handle(object $event): void
    {
        if ($event instanceof Login) {
            $this->logLogin($event);
        } elseif ($event instanceof Logout) {
            $this->logLogout($event);
        } elseif ($event instanceof Failed) {
            $this->logFailed($event);
        }
    }

    protected function logLogin(Login $event): void
    {
        $user = $event->user;

        AuthenticationLog::create([
            'authenticatable_type' => get_class($user),
            'authenticatable_id' => $user->getAuthIdentifier(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'login_at' => Carbon::now(),
            'login_successful' => true,
            'location' => [], // Implement IP geolocation if needed later
        ]);
    }

    protected function logLogout(Logout $event): void
    {
        $user = $event->user;

        if (! $user) {
            return;
        }

        // Find the latest successful login for this log entry to update logout time
        // Or create a new entry. Standard practice is usually updating the last 'active' log
        // But tracking logout as a separate event or updating the session log depends on preference.
        // Rappasoft updates the latest record where logout_at is null.

        $log = AuthenticationLog::where('authenticatable_id', $user->getAuthIdentifier())
            ->where('ip_address', $this->request->ip())
            ->where('user_agent', $this->request->userAgent())
            ->orderBy('login_at', 'desc')
            ->first();

        if ($log) {
            $log->update(['logout_at' => Carbon::now()]);
        }
    }

    protected function logFailed(Failed $event): void
    {
        $user = $event->user;

        AuthenticationLog::create([
            'authenticatable_type' => $user ? get_class($user) : 'Unknown', // 'Unknown' or record valid attempts for non-existing users? Standard practice: only if user exists or store attempted email.
            // If user is null (invalid email), we can't store authenticatable_id easily unless we store strict 'email'.
            // For now, let's only log if we have a user (wrong password) or skip.
            // Better: If user is known, log it.
            'authenticatable_id' => $user?->getAuthIdentifier(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'login_at' => Carbon::now(),
            'login_successful' => false,
        ]);
    }
}
