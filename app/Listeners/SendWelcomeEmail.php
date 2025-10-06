<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWelcomeEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EmailService $emailService
    ) {}

    public function handle(UserRegistered $event): void
    {
        $this->emailService->sendWelcomeEmail($event->user, $event->password);
    }
}
