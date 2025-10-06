<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminNewUser implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EmailService $emailService
    ) {}

    public function handle(UserRegistered $event): void
    {
        $admins = User::role('admin')->get();

        foreach ($admins as $admin) {
            $this->emailService->sendNewUserNotification($admin, $event->user);
        }
    }
}
