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
        // Buscar o nome do tenant do usuário (primeiro tenant)
        $tenantName = $event->user->tenants()->first()?->name;

        $this->emailService->sendWelcomeEmail($event->user, $event->password, $tenantName);
    }
}
