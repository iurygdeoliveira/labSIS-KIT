<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEmailVerificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private EmailService $emailService
    ) {}

    public function handle(UserRegistered $event): void
    {
        if (! $event->user->hasVerifiedEmail()) {
            $this->emailService->sendEmailVerification($event->user);
        }
    }
}
